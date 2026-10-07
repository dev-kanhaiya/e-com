<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    /**
     * API: GET /api/vendor/products
     * List vendor's own products with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::where('vendor_id', $request->user()->id)
            ->with(['category', 'primaryImage'])
            ->latest()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * API: POST /api/vendor/products
     * Vendor creates a new product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $user = $request->user();

        $product = Product::create([
            'vendor_id' => $user->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name).'-'.Str::random(6),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'sku' => $request->sku,
            'status' => $request->status ?? 'active',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'is_primary' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product->load(['category', 'images']),
        ], 201);
    }

    /**
     * API: PUT /api/vendor/products/{product}
     * Vendor updates own product.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
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

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'is_primary' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product->load(['category', 'images']),
        ]);
    }

    /**
     * API: DELETE /api/vendor/products/{product}
     * Vendor deletes own product.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $product = Product::where('id', $id)
            ->where('vendor_id', $request->user()->id)
            ->with('images')
            ->firstOrFail();

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
