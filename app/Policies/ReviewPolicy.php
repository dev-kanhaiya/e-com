<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, Review $review): bool
    {
        // Only author customer can update review
        return $user->id === $review->user_id;
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, Review $review): bool
    {
        // Admin or author customer can delete review. Vendor cannot!
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $review->user_id;
    }
}
