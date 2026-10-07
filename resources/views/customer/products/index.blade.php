@extends('layouts.app')

@section('title', 'Products')

@section('content')
<section class="hero">
  <h1>Shop from independent vendors</h1>
  <p>Quality products from verified local stores, all in one place.</p>
</section>

<form class="toolbar" method="GET" action="{{ route('customer.products.index') }}">
  <div class="field grow">
    <label for="search">Search</label>
    <input class="input" id="search" name="search" placeholder="Search products" value="{{ request('search') }}">
  </div>
  <div class="field">
    <label for="category">Category</label>
    <select class="input" id="category" name="category">
      <option value="">All categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="field">
    <label for="sort">Sort by</label>
    <select class="input" id="sort" name="sort">
      <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest arrivals</option>
      <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
      <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
    </select>
  </div>
  <button class="btn primary" type="submit">Apply filters</button>
  <a class="btn" href="{{ route('customer.products.index') }}">Reset</a>
</form>

<section class="grid">
  @forelse($products as $product)
    <article class="card">
      @php $primaryImg = $product->primaryImage; @endphp
      @if($primaryImg)
        <img class="ph" src="{{ asset('storage/' . $primaryImg->image) }}" alt="{{ $product->name }}" loading="lazy" style="object-fit:cover;width:100%">
      @else
        <div class="ph"></div>
      @endif
      <div class="body">
        <span class="cat">{{ $product->category->name }}</span>
        <h3>{{ $product->name }}</h3>
        <span class="vendor">{{ $product->vendor->vendorProfile->store_name ?? $product->vendor->name }}</span>
        <div class="row">
          <span class="price">₹{{ number_format($product->price, 0) }}</span>
          <span class="rating">★ {{ number_format($product->averageRating(), 1) }}</span>
        </div>
        <a class="btn" href="{{ route('customer.products.show', $product->slug) }}">View product</a>
      </div>
    </article>
  @empty
    <p style="color:var(--muted);grid-column:1/-1;text-align:center;padding:40px 0">No products found.</p>
  @endforelse
</section>

@if($products->hasPages())
  <div class="pagesIndex" style="padding:16px 0">{{ $products->links() }}</div>
@endif
@endsection
