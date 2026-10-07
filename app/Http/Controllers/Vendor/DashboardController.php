<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display vendor dashboard overview metrics.
     */
    public function index(Request $request): View
    {
        $vendorId = $request->user()->id;

        // Statistics for vendor products and sales
        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $totalOrderItems = OrderItem::where('vendor_id', $vendorId)->count();

        // Vendor's total revenue from order items
        $totalEarnings = OrderItem::where('vendor_id', $vendorId)
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->sum('total');

        // Recent orders involving vendor's products
        $recentOrderItems = OrderItem::with(['order.user', 'product'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact(
            'totalProducts',
            'totalOrderItems',
            'totalEarnings',
            'recentOrderItems'
        ));
    }
}
