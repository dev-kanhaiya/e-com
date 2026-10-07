<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store customer review for a purchased product.
     */
    public function store(StoreReviewRequest $request, int $productId): RedirectResponse
    {
        $user = $request->user();
        $product = Product::findOrFail($productId);

        // Check whether customer actually purchased this product
        $purchased = $user->orders()
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->first();

        if (! $purchased) {
            return back()->with('error', 'You can only review products you purchased.');
        }

        // Check if customer already submitted review for this product
        $existing = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $purchased->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'approved',
        ]);

        return back()->with('success', 'Review added successfully.');
    }

    /**
     * Update customer's own review.
     */
    public function update(UpdateReviewRequest $request, int $id): RedirectResponse
    {
        $review = Review::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review updated successfully.');
    }

    /**
     * Delete customer's own review.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $review = Review::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
