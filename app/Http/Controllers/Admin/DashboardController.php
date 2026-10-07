<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin overview metrics.
     */
    public function index(): View
    {
        // Calculate overview counts for admin dashboard
        $totalCustomers = User::where('role', 'customer')->count();
        $totalVendors = User::where('role', 'vendor')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();

        // Recent orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalVendors',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'recentOrders'
        ));
    }
}
