@extends('layouts.admin')
@section('title', 'Edit Product')

@section('admin-content')
<div class="head"><div><h1>Edit product</h1><p>{{ $product->name }}</p></div></div>

<section class="panel" style="padding:16px">
  <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
    @csrf @method('PUT')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
      <div class="field"><label for="name">Product name</label><input class="input" type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required></div>
      <div class="field"><label for="category_id">Category</label>
        <select class="input" id="category_id" name="category_id" required>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field"><label for="price">Price (₹)</label><input class="input" type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price) }}" required></div>
      <div class="field"><label for="stock">Stock quantity</label><input class="input" type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required></div>
      <div class="field"><label for="sku">SKU</label><input class="input" type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required></div>
      <div class="field"><label for="status">Status</label>
        <select class="input" id="status" name="status">
          <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
    </div>
    <div class="field" style="margin-top:16px"><label for="description">Description</label><textarea class="input" id="description" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea></div>
    <div style="display:flex;gap:8px;margin-top:16px">
      <button type="submit" class="btn primary">Save changes</button>
      <a class="btn" href="{{ route('admin.products.index') }}">Cancel</a>
    </div>
  </form>
</section>
@endsection
