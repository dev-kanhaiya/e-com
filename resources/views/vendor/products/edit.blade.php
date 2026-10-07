@extends('layouts.vendor')

@section('vendor-content')
<div class="wrap">
    <div class="head">
        <h1>Edit Product</h1>
        <a href="{{ route('vendor.products.index') }}" class="btn">Back to Products</a>
    </div>

    @if(session('success'))
        <div style="background: var(--ok-bg, #e6f4ea); color: var(--ok, #137333); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: var(--bad-bg, #fce8e6); color: var(--bad, #c5221f); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel body" style="max-width: 800px; margin-bottom: 2rem;">
        <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="field">
                <label>Product Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="field">
                <label>Category</label>
                <select name="category_id" class="input" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" class="input" rows="5" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="input" value="{{ old('price', $product->price) }}" required>
                </div>
                <div class="field">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" class="input" value="{{ old('stock', $product->stock) }}" required>
                </div>
            </div>

            <div class="field">
                <label>Status</label>
                <select name="status" class="input">
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ old('status', $product->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="field">
                <label>Add New Images</label>
                <input type="file" name="images[]" class="input" multiple accept="image/*">
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn primary">Update Product</button>
            </div>
        </form>
    </section>

    @if($product->images && $product->images->count() > 0)
        <div class="head">
            <h2>Manage Images</h2>
        </div>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach($product->images as $image)
                <div class="panel body" style="text-align: center; position: relative; padding: 12px;">
                    @if($image->is_primary)
                        <span class="badge ok" style="position: absolute; top: 18px; left: 18px;">Primary</span>
                    @endif
                    <img src="{{ asset('storage/' . $image->image) }}" alt="Product Image" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px; margin-bottom: 0.75rem;">
                    <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                        @if(!$image->is_primary)
                            <form action="{{ route('vendor.products.images.primary', ['product' => $product->id, 'image' => $image->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn sm">Set Primary</button>
                            </form>
                        @endif
                        <form action="{{ route('vendor.products.images.destroy', ['product' => $product->id, 'image' => $image->id]) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn sm danger">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
