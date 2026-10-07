<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display public/customer product catalog with search, filter, and pagination.
     */
    public function index(Request $request): View
    {
        $categories = Category::where('status', 'active')->get();

        // Query active products
        $query = Product::with(['category', 'primaryImage', 'vendor.vendorProfile'])
            ->where('status', 'active');

        // Search by product name or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by min price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Sort products
        switch ($request->sort) {
            case 'price_low':
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('customer.products.index', compact('products', 'categories'));
    }

    /**
     * Display product details with images, vendor information, and reviews.
     */
    public function show(string $slug): View
    {
        $product = Product::with([
            'category',
            'vendor.vendorProfile',
            'images',
            'reviews.user',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Customer's purchase status check to enable review option
        $hasPurchased = false;
        if (auth()->check() && auth()->user()->isCustomer()) {
            $hasPurchased = auth()->user()->orders()
                ->whereHas('items', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->exists();
        }

        return view('customer.products.show', compact('product', 'hasPurchased'));
    }
}
