<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * API: GET /api/cart
     * View logged-in user's cart items.
     */
    public function index(Request $request): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $items = $cart->items()->with('product.primaryImage')->get();

        $subtotal = $cart->totalAmount();
        $shipping = $subtotal > 0 ? 50.00 : 0.00;
        $total = $subtotal + $shipping;

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
            ],
        ]);
    }

    /**
     * API: POST /api/cart/items
     * Add product to cart with stock validation.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Product is inactive or unavailable.',
            ], 422);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        $requestedQty = (int) $request->quantity;
        $newQty = $cartItem ? ($cartItem->quantity + $requestedQty) : $requestedQty;

        if ($product->stock < $newQty) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock. Only {$product->stock} available.",
            ], 422);
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'price' => $product->price,
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $requestedQty,
                'price' => $product->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart.',
            'data' => $cartItem->load('product'),
        ], 201);
    }

    /**
     * API: PATCH /api/cart/items/{item}
     * Update cart item quantity.
     */
    public function update(Request $request, int $itemId): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        if ($request->quantity > $item->product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock. Only {$item->product->stock} available.",
            ], 422);
        }

        $item->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated.',
            'data' => $item,
        ]);
    }

    /**
     * API: DELETE /api/cart/items/{item}
     * Remove item from cart.
     */
    public function destroy(Request $request, int $itemId): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
        ]);
    }
}
