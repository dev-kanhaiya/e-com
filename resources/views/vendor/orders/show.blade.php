@extends('layouts.vendor')

@section('vendor-content')
<div class="wrap">
    <div class="head">
        <h1>Order #{{ $order->id }}</h1>
        <a href="{{ route('vendor.orders.index') }}" class="btn">Back to Orders</a>
    </div>

    @if(session('success'))
        <div style="background: var(--ok-bg, #e6f4ea); color: var(--ok, #137333); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <div class="head">
                <h2>Items in this Order</h2>
            </div>
            <section class="panel">
                <div class="scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="pcell">
                                            @if($item->product && $item->product->images && $item->product->images->count() > 0)
                                                <img src="{{ Storage::url($item->product->images->first()->image_path) }}" class="thumb" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="ph" style="width:40px; height:40px; border-radius:4px;"></div>
                                            @endif
                                            <div class="name">{{ $item->product_name ?? ($item->product->name ?? 'Unknown') }}</div>
                                        </div>
                                    </td>
                                    <td>₹{{ number_format($item->price, 0) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₹{{ number_format($item->price * $item->quantity, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div>
            <section class="panel body" style="margin-bottom: 2rem;">
                <h3>Customer Info</h3>
                <p><strong>{{ $order->shippingAddress->name ?? ($order->user->name ?? 'Unknown') }}</strong></p>
                <p>Email: {{ $order->user->email ?? 'N/A' }}</p>
                @if($order->shippingAddress)
                    <p>{{ $order->shippingAddress->address }}</p>
                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                    <p>Phone: {{ $order->shippingAddress->phone }}</p>
                @endif
            </section>

            <section class="panel body">
                <h3>Update Order Status</h3>
                <form action="{{ route('vendor.orders.status.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="field">
                        <label>Current Status</label>
                        <select name="status" class="input">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div style="margin-top: 1rem;">
                        <button type="submit" class="btn primary">Update Status</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
