<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show vendor store profile.
     */
    public function show(Request $request): View
    {
        $vendor  = $request->user();
        $profile = $vendor->vendorProfile;

        return view('vendor.profile', compact('vendor', 'profile'));
    }

    /**
     * Update vendor store profile and/or account details.
     */
    public function update(Request $request): RedirectResponse
    {
        $vendor  = $request->user();
        $profile = $vendor->vendorProfile;

        $request->validate([
            // Account fields
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'max:255', 'unique:users,email,' . $vendor->id],
            'current_password'     => ['nullable', 'string'],
            'password'             => ['nullable', 'string', 'min:6', 'confirmed'],
            // Store fields
            'store_name'           => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:1000'],
            'phone'                => ['nullable', 'string', 'max:50'],
            'address'              => ['nullable', 'string', 'max:255'],
        ]);

        // Update account
        $vendor->name  = $request->name;
        $vendor->email = $request->email;

        if ($request->filled('password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $vendor->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $vendor->password = Hash::make($request->password);
        }

        $vendor->save();

        // Update store profile
        if ($profile) {
            $profile->update([
                'store_name'  => $request->store_name,
                'store_slug'  => Str::slug($request->store_name) . '-' . $vendor->id,
                'description' => $request->description,
                'phone'       => $request->phone,
                'address'     => $request->address,
            ]);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
