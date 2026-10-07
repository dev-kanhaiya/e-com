<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display listing of vendor's own products only.
     */
    public function index(Request $request): View
    {
        $vendorId = $request->user()->id;

        $products = Product::with(['category', 'primaryImage', 'images'])
            ->where('vendor_id', $vendorId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vendor.products.index', compact('products'));
    }

    /**
     * Show form to create new product.
     */
    public function create(): View
    {
        $categories = Category::where('status', 'active')->get();

        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Store new product for vendor.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $vendor = $request->user();

        $sku = $request->sku ?: ('SKU-' . strtoupper(Str::random(8)));

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.Str::random(6),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'sku' => $sku,
            'status' => $request->status ?? 'active',
        ]);

        // Handle single image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'is_primary' => true,
            ]);
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            $isFirst = ! $request->hasFile('image');
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => $isFirst,
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('vendor.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show form to edit vendor's product with ownership check.
     */
    public function edit(Request $request, int $id): View
    {
        // Enforce vendor ownership
        $product = Product::where('id', $id)
            ->where('vendor_id', $request->user()->id)
            ->with('images')
            ->firstOrFail();

        $categories = Category::where('status', 'active')->get();

        return view('vendor.products.edit', compact('product', 'categories'));
    }

    /**
     * Update vendor's product.
     */
    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        // Enforce vendor ownership
        $product = Product::where('id', $id)
            ->where('vendor_id', $request->user()->id)
            ->firstOrFail();

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

        // Handle additional image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'is_primary' => ! $hasPrimary,
            ]);
        }

        if ($request->hasFile('images')) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_primary' => ! $hasPrimary,
                ]);
                $hasPrimary = true;
            }
        }

        return redirect()->route('vendor.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Delete vendor product.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $product = Product::where('id', $id)
            ->where('vendor_id', $request->user()->id)
            ->with('images')
            ->firstOrFail();

        // Delete physical images from storage
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image);
        }

        $product->delete();

        return redirect()->route('vendor.products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Set primary image for product.
     */
    public function setPrimaryImage(Request $request, int $productId, int $imageId): RedirectResponse
    {
        $product = Product::where('id', $productId)
            ->where('vendor_id', $request->user()->id)
            ->firstOrFail();

        $image = ProductImage::where('id', $imageId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }

    /**
     * Delete an individual product image.
     */
    public function destroyImage(Request $request, int $productId, int $imageId): RedirectResponse
    {
        $product = Product::where('id', $productId)
            ->where('vendor_id', $request->user()->id)
            ->firstOrFail();

        $image = ProductImage::where('id', $imageId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        // Delete physical file
        Storage::disk('public')->delete($image->image);
        $image->delete();

        // Ensure at least one image remains primary if any images exist
        if ($image->is_primary) {
            $firstImage = $product->images()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Image deleted successfully.');
    }
}
