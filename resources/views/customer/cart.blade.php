@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="head">
  <div>
    <h1>Your Cart</h1>
    <p>Review your items before checkout.</p>
  </div>
  @if($items->count() > 0)
    <form action="{{ route('cart.clear') }}" method="POST">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn danger">Clear Cart</button>
    </form>
  @endif
</div>

@if($items->count() > 0)
  <section class="panel">
    <div class="scroll">
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th class="act">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $item)
            <tr>
              <td>
                <div class="pcell">
                  @php $primaryImg = $item->product->primaryImage; @endphp
                  @if($primaryImg)
                    <img class="thumb" src="{{ asset('storage/' . $primaryImg->image) }}" alt="{{ $item->product->name }}" style="object-fit:cover">
                  @else
                    <span class="thumb"></span>
                  @endif
                  <div>
                    <a href="{{ route('customer.products.show', $item->product->slug) }}" class="link">{{ $item->product->name }}</a>
                    <div class="sub">{{ $item->product->vendor->vendorProfile->store_name ?? $item->product->vendor->name }}</div>
                  </div>
                </div>
              </td>
              <td>₹{{ number_format($item->price, 0) }}</td>
              <td>
                <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:flex;gap:8px;align-items:center">
                  @csrf
                  @method('PATCH')
                  <input class="input" type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" style="width:70px">
                  <button type="submit" class="btn sm">Update</button>
                </form>
              </td>
              <td><strong>₹{{ number_format($item->price * $item->quantity, 0) }}</strong></td>
              <td class="act">
                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn sm danger">Remove</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div style="padding:20px;border-top:1px solid var(--border);display:flex;flex-direction:column;align-items:flex-end;gap:8px">
      <div>Subtotal: <strong>₹{{ number_format($subtotal, 0) }}</strong></div>
      <div>Shipping: <strong>₹{{ number_format($shipping, 0) }}</strong></div>
      <div style="font-size:20px;margin-top:4px">Total: <strong style="color:var(--accent)">₹{{ number_format($total, 0) }}</strong></div>
      <div style="margin-top:12px">
        <a href="{{ route('checkout.index') }}" class="btn primary">Proceed to Checkout</a>
      </div>
    </div>
  </section>
@else
  <section class="panel" style="padding:40px;text-align:center">
    <p style="color:var(--muted);margin-bottom:16px">Your cart is empty.</p>
    <a href="{{ route('customer.products.index') }}" class="btn primary">Continue Shopping</a>
  </section>
@endif
@endsection
