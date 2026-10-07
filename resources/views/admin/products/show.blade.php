@extends('layouts.admin')
@section('title', 'Product Details')

@section('admin-content')
<div class="head">
  <div><h1>{{ $product->name }}</h1><p>Product details and reviews</p></div>
  <div style="display:flex;gap:8px">
    <a class="btn primary" href="{{ route('admin.products.edit', $product->id) }}">Edit product</a>
    <a class="btn" href="{{ route('admin.products.index') }}">Back to products</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  {{-- Product Info --}}
  <section class="panel" style="padding:16px">
    <h2 style="font-size:16px;margin-bottom:16px">Product information</h2>
    <div style="display:flex;flex-direction:column;gap:12px">
      <div><span style="font-size:13px;color:var(--muted);display:block">Status</span>
        <span class="badge {{ $product->status === 'active' ? 'ok' : 'bad' }}">{{ ucfirst($product->status) }}</span>
      </div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Category</span>{{ $product->category->name }}</div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Vendor</span>{{ $product->vendor->vendorProfile->store_name ?? $product->vendor->name }}</div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Price</span><strong style="font-size:20px">₹{{ number_format($product->price, 0) }}</strong></div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Stock</span>{{ $product->stock }} items</div>
      <div><span style="font-size:13px;color:var(--muted);display:block">SKU</span><code>{{ $product->sku }}</code></div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Description</span>{{ $product->description }}</div>
    </div>
  </section>

  <div>
    {{-- Images --}}
    <section class="panel" style="padding:16px;margin-bottom:20px">
      <h2 style="font-size:16px;margin-bottom:12px">Product images ({{ $product->images->count() }})</h2>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
        @forelse($product->images as $img)
          <div style="position:relative">
            <img src="{{ asset('storage/' . $img->image) }}" alt="Product" loading="lazy" style="width:100%;height:90px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
            @if($img->is_primary)
              <span class="badge ok" style="position:absolute;top:4px;left:4px">Primary</span>
            @endif
          </div>
        @empty
          <p style="color:var(--muted);font-size:13px;grid-column:1/-1">No images uploaded.</p>
        @endforelse
      </div>
    </section>

    {{-- Reviews --}}
    <section class="panel" style="padding:16px">
      <h2 style="font-size:16px;margin-bottom:12px">Customer reviews ({{ $product->reviews->count() }})</h2>
      @forelse($product->reviews as $rev)
        <div style="padding:12px 0;border-bottom:1px solid var(--border)">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <strong>{{ $rev->user->name }}</strong>
            <span style="color:var(--warn)">
              @for($i = 1; $i <= 5; $i++){{ $i <= $rev->rating ? '★' : '☆' }}@endfor
            </span>
          </div>
          <p style="color:var(--muted);font-size:13px;margin-top:4px">{{ $rev->comment }}</p>
        </div>
      @empty
        <p style="color:var(--muted);font-size:13px">No reviews yet.</p>
      @endforelse
    </section>
  </div>
</div>
@endsection
