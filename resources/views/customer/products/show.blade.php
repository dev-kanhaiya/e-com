@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="head">
  <div>
    <h1>{{ $product->name }}</h1>
    <p>By {{ $product->vendor->vendorProfile->store_name ?? $product->vendor->name }} in {{ $product->category->name }}</p>
  </div>
  <div class="row" style="gap:16px;align-items:center">
    <span class="rating" style="font-size:18px">
      @php $rating = round($product->averageRating()); @endphp
      @for($i=1; $i<=5; $i++)
        @if($i <= $rating) ★ @else ☆ @endif
      @endfor
      ({{ number_format($product->averageRating(), 1) }})
    </span>
    <span class="price" style="font-size:24px">₹{{ number_format($product->price, 0) }}</span>
  </div>
</div>

@if(session('success'))
  <div style="background:var(--ok-bg);color:var(--ok);padding:12px;border-radius:6px;margin-bottom:16px">
    {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div style="background:var(--bad-bg);color:var(--bad);padding:12px;border-radius:6px;margin-bottom:16px">
    {{ session('error') }}
  </div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:32px">
  <div>
    @php $primaryImg = $product->primaryImage; @endphp
    @if($primaryImg)
      <img id="mainImage" src="{{ asset('storage/' . $primaryImg->image) }}" alt="{{ $product->name }}" style="width:100%;height:400px;object-fit:cover;border-radius:8px;margin-bottom:16px">
    @else
      <div class="ph" style="height:400px;border-radius:8px;margin-bottom:16px"></div>
    @endif
    
    @if($product->images->count() > 1)
      <div style="display:flex;gap:8px;overflow-x:auto">
        @foreach($product->images as $img)
          <img src="{{ asset('storage/' . $img->image) }}" alt="Thumbnail" onclick="changeMainImage('{{ asset('storage/' . $img->image) }}')" style="width:80px;height:80px;object-fit:cover;border-radius:4px;cursor:pointer">
        @endforeach
      </div>
    @endif
  </div>

  <div>
    <section class="panel" style="padding:24px;margin-bottom:24px">
      <h3 style="margin-bottom:16px">Product Details</h3>
      <p style="white-space:pre-wrap;color:var(--muted)">{{ $product->description }}</p>
      
      <p style="margin-top:16px"><strong>Stock:</strong> {{ $product->stock }} available</p>
    </section>

    <section class="panel" style="padding:24px">
      <form action="{{ route('cart.add') }}" method="POST" style="display:flex;gap:12px;align-items:flex-end">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <div class="field grow">
          <label for="quantity">Quantity</label>
          <input class="input" type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}">
        </div>
        <button type="submit" class="btn primary" {{ $product->stock < 1 ? 'disabled' : '' }}>
          Add to Cart
        </button>
      </form>
    </section>
  </div>
</div>

<div class="head">
  <div>
    <h2>Customer Reviews</h2>
  </div>
</div>

<section class="panel" style="padding:24px">
  @forelse($product->reviews()->where('is_approved', true)->latest()->get() as $review)
    <div style="border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:16px">
      <div class="row" style="justify-content:space-between;margin-bottom:8px">
        <strong>{{ $review->user->name }}</strong>
        <span class="rating">
          @for($i=1; $i<=5; $i++)
            @if($i <= $review->rating) ★ @else ☆ @endif
          @endfor
        </span>
      </div>
      <p style="color:var(--muted);margin:0">{{ $review->comment }}</p>
    </div>
  @empty
    <p style="color:var(--muted);text-align:center">No reviews yet.</p>
  @endforelse

  @if($hasPurchased)
    <h3 style="margin:24px 0 16px">Write a Review</h3>
    
    @if($errors->any())
      <div style="background:var(--bad-bg);color:var(--bad);padding:12px;border-radius:6px;margin-bottom:16px">
        <ul style="margin:0;padding-left:20px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('customer.reviews.store', $product) }}" method="POST">
      @csrf
      <div class="field" style="margin-bottom:16px">
        <label for="rating">Rating</label>
        <select class="input" id="rating" name="rating" required>
          <option value="5">5 Stars</option>
          <option value="4">4 Stars</option>
          <option value="3">3 Stars</option>
          <option value="2">2 Stars</option>
          <option value="1">1 Star</option>
        </select>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label for="comment">Comment</label>
        <textarea class="input" id="comment" name="comment" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn primary">Submit Review</button>
    </form>
  @endif
</section>

<script>
  function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
  }
</script>
@endsection
