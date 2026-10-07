<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of all customer reviews.
     */
    public function index(Request $request): View
    {
        $reviews = Review::with(['user', 'product'])
            ->when($request->search, function ($query, $search) {
                $query->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Delete inappropriate review.
     */
    public function destroy(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }
}
