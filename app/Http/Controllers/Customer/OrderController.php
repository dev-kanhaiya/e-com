<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display list of customer's own orders.
     */
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display customer order details.
     * Enforces ownership so customer cannot view other customers' orders.
     */
    public function show(Request $request, int $id): View
    {
        $order = $request->user()->orders()
            ->with(['items.product.primaryImage', 'items.vendor.vendorProfile', 'payment'])
            ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }
}
