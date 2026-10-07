@extends('layouts.admin')
@section('title', 'All Products')

@section('admin-content')
<div class="head"><div><h1>Products catalog</h1><p>Overview and moderation of products listed by all vendors</p></div></div>

<form class="toolbar" method="GET" action="{{ route('admin.products.index') }}">
  <div class="field grow"><label for="search">Search</label><input class="input" id="search" name="search" placeholder="Search by name or SKU" value="{{ request('search') }}"></div>
  <div class="field"><label for="category">Category</label>
    <select class="input" id="category" name="category">
      <option value="">All categories</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="field"><label for="status">Status</label>
    <select class="input" id="status" name="status">
      <option value="">All statuses</option>
      <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>
  <button class="btn primary" type="submit">Filter</button>
  <a class="btn" href="{{ route('admin.products.index') }}">Reset</a>
</form>

<section class="panel"><div class="scroll"><table>
  <thead><tr><th>Product</th><th>Vendor</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="act">Actions</th></tr></thead>
  <tbody>
    @forelse($products as $product)
      <tr>
        <td><div class="pcell">
          @if($product->primaryImage)
            <img class="thumb" src="{{ asset('storage/' . $product->primaryImage->image) }}" alt="{{ $product->name }}" loading="lazy" style="object-fit:cover">
          @else
            <span class="thumb"></span>
          @endif
          <div>{{ $product->name }}<div class="sub">{{ $product->sku }}</div></div>
        </div></td>
        <td>{{ $product->vendor->vendorProfile->store_name ?? $product->vendor->name }}</td>
        <td>{{ $product->category->name }}</td>
        <td>₹{{ number_format($product->price, 0) }}</td>
        <td><span class="badge {{ $product->stock > 0 ? 'ok' : 'bad' }}">{{ $product->stock }} in stock</span></td>
        <td><span class="badge {{ $product->status === 'active' ? 'ok' : 'bad' }}">{{ ucfirst($product->status) }}</span></td>
        <td class="act">
          <a class="btn sm" href="{{ route('admin.products.show', $product->id) }}">View</a>
          <a class="btn sm" href="{{ route('admin.products.edit', $product->id) }}">Edit</a>
          <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this product?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn sm danger">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No products found.</td></tr>
    @endforelse
  </tbody>
</table></div></section>

@if($products->hasPages())
  <div style="padding:16px">{{ $products->links() }}</div>
@endif
@endsection
