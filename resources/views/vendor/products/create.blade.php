@extends('layouts.vendor')

@section('vendor-content')
<div class="wrap">
    <div class="head">
        <h1>Add New Product</h1>
        <a href="{{ route('vendor.products.index') }}" class="btn">Back to Products</a>
    </div>

    @if($errors->any())
        <div style="background: var(--bad-bg, #fce8e6); color: var(--bad, #c5221f); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel body" style="max-width: 800px;">
        <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="field">
                <label>Product Name</label>
                <input type="text" name="name" class="input" value="{{ old('name') }}" required>
            </div>

            <div class="field">
                <label>Category</label>
                <select name="category_id" class="input" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" class="input" rows="5" required>{{ old('description') }}</textarea>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="input" value="{{ old('price') }}" required>
                </div>
                <div class="field">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" class="input" value="{{ old('stock') }}" required>
                </div>
                <div class="field">
                    <label>SKU (Optional)</label>
                    <input type="text" name="sku" class="input" value="{{ old('sku') }}" placeholder="Auto-generated if empty">
                </div>
            </div>

            <div class="field">
                <label>Product Images</label>
                <input type="file" name="images[]" class="input" multiple accept="image/*">
                <div class="sub" style="margin-top: 0.5rem;">You can select multiple images.</div>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn primary">Create Product</button>
            </div>
        </form>
    </section>
</div>
@endsection
