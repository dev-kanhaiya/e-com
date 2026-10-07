<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display list of orders that contain products belonging to the logged-in vendor.
     */
    public function index(Request $request): View
    {
        $vendorId = $request->user()->id;

        // Fetch orders containing products of this vendor
        $orders = Order::whereHas('items', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->with(['user', 'payment'])
            ->latest()
            ->paginate(10);

        return view('vendor.orders.index', compact('orders'));
    }

    /**
     * Export vendor orders safely via streaming CSV.
     */
    public function export(Request $request)
    {
        $vendorId = $request->user()->id;
        $filename = 'vendor_orders_' . date('Y-m-d_His') . '.csv';

        $query = Order::whereHas('items', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->with(['user', 'payment', 'items' => function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            }])
            ->latest();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Order Number',
                'Customer Name',
                'Your Store Items',
                'Items Quantity',
                'Store Items Total (INR)',
                'Fulfillment Status',
                'Payment Status',
                'Placed At',
            ]);

            $query->chunk(500, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $itemNames = $order->items->pluck('product_name')->implode(', ');
                    $itemQty = $order->items->sum('quantity');
                    $storeTotal = $order->items->sum('total');

                    fputcsv($handle, [
                        $order->order_number,
                        $order->user ? $order->user->name : 'N/A',
                        $itemNames,
                        $itemQty,
                        $storeTotal,
                        ucfirst($order->status),
                        ucfirst($order->payment_status),
                        $order->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Display order details showing only vendor's items.
     */
    public function show(Request $request, int $id): View
    {
        $vendorId = $request->user()->id;

        $order = Order::whereHas('items', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })
            ->with(['user', 'payment', 'items' => function ($query) use ($vendorId) {
                // Only show this vendor's items
                $query->where('vendor_id', $vendorId)->with('product');
            }])
            ->findOrFail($id);

        return view('vendor.orders.show', compact('order'));
    }

    /**
     * Update order status by vendor (if permissible).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $vendorId = $request->user()->id;

        $order = Order::whereHas('items', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:processing,shipped,delivered,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully.');
    }
}
