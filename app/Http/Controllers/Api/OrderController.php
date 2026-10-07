<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * API: GET /api/orders
     * List user orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * API: GET /api/orders/{order}
     * Get specific order details.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $request->user()->orders()
            ->with(['items.product', 'payment'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * API: POST /api/orders
     * Create order from customer's cart.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cod,stripe',
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.',
            ], 422);
        }

        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $formattedAddress = "{$address->name}, {$address->phone}\n{$address->address}, {$address->city}, {$address->state} - {$address->postal_code}, {$address->country}";

        $subtotal = 0;
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$item->product->name}.",
                ], 422);
            }
            $subtotal += ($item->product->price * $item->quantity);
        }

        $shippingFee = 50.00;
        $total = $subtotal + $shippingFee;

        // DB transaction
        $order = DB::transaction(function () use (
            $user,
            $cart,
            $subtotal,
            $shippingFee,
            $total,
            $request,
            $formattedAddress
        ) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => 0,
                'total' => $total,
                'status' => 'confirmed',
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'paid',
                'shipping_address' => $formattedAddress,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'vendor_id' => $item->product->vendor_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'total' => $item->product->price * $item->quantity,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $request->payment_method === 'cod' ? null : ('tx_'.Str::random(12)),
                'amount' => $total,
                'method' => $request->payment_method,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'paid',
                'paid_at' => $request->payment_method === 'cod' ? null : now(),
            ]);

            $cart->items()->delete();

            return $order;
        });

        MailService::sendOrderConfirmation($order);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
            'data' => $order->load('items'),
        ], 201);
    }
}
