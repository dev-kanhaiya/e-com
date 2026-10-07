<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * API: POST /api/products/{product}/reviews
     * Add review for purchased product.
     */
    public function store(StoreReviewRequest $request, int $productId): JsonResponse
    {
        $user = $request->user();
        $product = Product::findOrFail($productId);

        $purchased = $user->orders()
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->first();

        if (! $purchased) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review products you purchased.',
            ], 403);
        }

        $existing = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product.',
            ], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $purchased->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review added successfully.',
            'data' => $review,
        ], 201);
    }

    /**
     * API: PUT /api/reviews/{review}
     * Update customer's own review.
     */
    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = Review::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully.',
            'data' => $review,
        ]);
    }

    /**
     * API: DELETE /api/reviews/{review}
     * Delete customer review.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = Review::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }
}
