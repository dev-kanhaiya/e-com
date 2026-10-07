<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Customer can only view their own order
        if ($user->isCustomer()) {
            return $order->user_id === $user->id;
        }

        // Vendor can view if the order contains any of their items
        if ($user->isVendor()) {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the order status.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isVendor()) {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }
}
