<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Admin can view all, vendor can view if active or vendor's own, customer can view active.
     */
    public function view(?User $user, Product $product): bool
    {
        if ($product->status === 'active') {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isVendor() && $product->vendor_id === $user->id;
    }

    /**
     * Vendor can create product (if approved).
     */
    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isVendor()) {
            // Vendor must have an approved profile
            return $user->vendorProfile && $user->vendorProfile->status === 'approved';
        }

        return false;
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Vendor can only update their own product
        return $user->isVendor() && $product->vendor_id === $user->id;
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Vendor can only delete their own product
        return $user->isVendor() && $product->vendor_id === $user->id;
    }
}
