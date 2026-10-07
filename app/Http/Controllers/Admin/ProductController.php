<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of all products for admin.
     */
    public function index(Request $request): View
    {
        $categories = Category::all();

        $products = Product::with(['category', 'vendor.vendorProfile', 'primaryImage'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): View
    {
        $product = Product::with(['category', 'vendor.vendorProfile', 'images', 'reviews.user'])
            ->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show edit form for product.
     */
    public function edit(int $id): View
    {
        $product = Product::with(['images', 'category'])->findOrFail($id);
        $categories = Category::where('status', 'active')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product details or status.
     */
    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.$product->id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'sku' => $request->sku,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Delete product.
     */
    public function destroy(int $id): RedirectResponse
    {
        $product = Product::with('images')->findOrFail($id);

        // Delete physical images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
