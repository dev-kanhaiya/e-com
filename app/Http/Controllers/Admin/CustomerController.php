<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request): View
    {
        $customers = User::withCount('orders')
            ->where('role', 'customer')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display customer details with their orders and addresses.
     */
    public function show(int $id): View
    {
        $customer = User::with(['addresses', 'orders.items'])->where('role', 'customer')->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Block customer.
     */
    public function block(int $id): RedirectResponse
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->update(['status' => 'blocked']);

        return back()->with('success', 'Customer blocked successfully.');
    }

    /**
     * Unblock customer.
     */
    public function unblock(int $id): RedirectResponse
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->update(['status' => 'active']);

        return back()->with('success', 'Customer unblocked successfully.');
    }
}
