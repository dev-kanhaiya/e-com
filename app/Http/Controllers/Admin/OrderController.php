<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of all customer orders.
     */
    public function index(Request $request): View
    {
        $orders = Order::with(['user', 'payment'])
            ->when($request->search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Export orders (with current filters applied) safely via streaming CSV.
     * Handles large datasets without memory exhaustion.
     */
    public function export(Request $request)
    {
        $filename = 'orders_report_' . date('Y-m-d_His') . '.csv';

        $query = Order::with(['user', 'payment'])
            ->when($request->search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->payment_status, function ($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
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
                'Customer Email',
                'Subtotal (INR)',
                'Shipping (INR)',
                'Total Amount (INR)',
                'Fulfillment Status',
                'Payment Status',
                'Payment Method',
                'Transaction ID',
                'Placed At',
            ]);

            // Memory-efficient chunking for 100k+ rows
            $query->chunk(500, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->order_number,
                        $order->user ? $order->user->name : 'N/A',
                        $order->user ? $order->user->email : 'N/A',
                        $order->subtotal,
                        $order->shipping_fee,
                        $order->total,
                        ucfirst($order->status),
                        ucfirst($order->payment_status),
                        $order->payment ? strtoupper($order->payment->method) : 'N/A',
                        $order->payment ? $order->payment->transaction_id : 'N/A',
                        $order->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Display order details.
     */
    public function show(int $id): View
    {
        $order = Order::with(['user', 'items.product', 'items.vendor', 'payment'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status or payment status.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,failed'],
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        if ($order->payment) {
            $order->payment->update([
                'status' => $request->payment_status,
                'paid_at' => $request->payment_status === 'paid' ? now() : $order->payment->paid_at,
            ]);
        }

        return back()->with('success', 'Order status updated successfully.');
    }
}
