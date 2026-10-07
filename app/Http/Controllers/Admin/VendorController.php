<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(Request $request): View
    {
        $vendors = User::with('vendorProfile')
            ->where('role', 'vendor')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('vendorProfile', function ($vp) use ($search) {
                            $vp->where('store_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show form to create a new vendor.
     */
    public function create(): View
    {
        return view('admin.vendors.create');
    }

    /**
     * Store newly created vendor.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'store_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $vendor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'vendor',
            'status' => 'active',
        ]);

        \App\Models\VendorProfile::create([
            'user_id' => $vendor->id,
            'store_name' => $request->store_name,
            'store_slug' => \Illuminate\Support\Str::slug($request->store_name) . '-' . \Illuminate\Support\Str::random(5),
            'description' => $request->description,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'approved',
        ]);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor details.
     */
    public function show(int $id): View
    {
        $vendor = User::with(['vendorProfile', 'products.category'])->where('role', 'vendor')->findOrFail($id);

        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Show form to edit vendor.
     */
    public function edit(int $id): View
    {
        $vendor = User::with('vendorProfile')->where('role', 'vendor')->findOrFail($id);

        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Remove vendor.
     */
    public function destroy(int $id): RedirectResponse
    {
        $vendor = User::where('role', 'vendor')->findOrFail($id);
        $vendor->delete();

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Update vendor information or approval status.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $vendor = User::with('vendorProfile')->where('role', 'vendor')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$vendor->id],
            'store_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,blocked'],
            'profile_status' => ['required', 'in:pending,approved,rejected,blocked'],
        ]);

        $vendor->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        if ($vendor->vendorProfile) {
            $vendor->vendorProfile->update([
                'store_name' => $request->store_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->profile_status,
            ]);
        }

        return redirect()->route('admin.vendors.show', $vendor->id)->with('success', 'Vendor updated successfully.');
    }

    /**
     * Approve vendor.
     */
    public function approve(int $id): RedirectResponse
    {
        $vendor = User::with('vendorProfile')->where('role', 'vendor')->findOrFail($id);
        if ($vendor->vendorProfile) {
            $vendor->vendorProfile->update(['status' => 'approved']);
        }

        return back()->with('success', 'Vendor approved successfully.');
    }

    /**
     * Reject vendor.
     */
    public function reject(int $id): RedirectResponse
    {
        $vendor = User::with('vendorProfile')->where('role', 'vendor')->findOrFail($id);
        if ($vendor->vendorProfile) {
            $vendor->vendorProfile->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Vendor rejected successfully.');
    }

    /**
     * Block vendor.
     */
    public function block(int $id): RedirectResponse
    {
        $vendor = User::where('role', 'vendor')->findOrFail($id);
        $vendor->update(['status' => 'blocked']);

        return back()->with('success', 'Vendor blocked successfully.');
    }

    /**
     * Unblock vendor.
     */
    public function unblock(int $id): RedirectResponse
    {
        $vendor = User::where('role', 'vendor')->findOrFail($id);
        $vendor->update(['status' => 'active']);

        return back()->with('success', 'Vendor unblocked successfully.');
    }
}
