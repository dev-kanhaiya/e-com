<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Display list of customer addresses.
     */
    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return view('customer.addresses.index', compact('addresses'));
    }

    /**
     * Show form to create new address.
     */
    public function create(): View
    {
        return view('customer.addresses.create');
    }

    /**
     * Store new customer address.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $request->user();

        // If first address, make it default automatically
        $isFirst = $user->addresses()->count() === 0;

        $user->addresses()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'India',
            'is_default' => $request->boolean('is_default') || $isFirst,
        ]);

        return redirect()->route('addresses.index')->with('success', 'Address added successfully.');
    }

    /**
     * Show edit form for address.
     */
    public function edit(Request $request, int $id): View
    {
        $address = $request->user()->addresses()->findOrFail($id);

        return view('customer.addresses.edit', compact('address'));
    }

    /**
     * Update address.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'India',
            'is_default' => $request->boolean('is_default') || $address->is_default,
        ]);

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully.');
    }

    /**
     * Delete address.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return redirect()->route('addresses.index')->with('success', 'Address deleted successfully.');
    }

    /**
     * Set address as default.
     */
    public function setDefault(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $user->addresses()->update(['is_default' => false]);

        $address = $user->addresses()->findOrFail($id);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }
}
