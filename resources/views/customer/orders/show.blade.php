@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="wrap">
    <div class="head">
        <div>
            <h1>Order {{ $order->order_number }}</h1>
            <p>Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <a href="{{ route('customer.orders.index') }}" class="btn">Back to Orders</a>
    </div>

    <section class="panel body" style="margin-bottom: 1.5rem; padding: 20px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem">
            <div>
                <h3 style="margin-bottom:10px">Shipping Details</h3>
                <p style="white-space:pre-line;color:var(--muted);font-size:14px">{{ $order->shipping_address }}</p>
            </div>
            <div>
                <h3 style="margin-bottom:10px">Order Summary</h3>
                <p style="margin-bottom:6px">Status: 
                    @php
                        $statusClass = 'badge';
                        if(in_array(strtolower($order->status), ['delivered', 'active', 'approved'])) $statusClass = 'badge ok';
                        elseif(in_array(strtolower($order->status), ['pending'])) $statusClass = 'badge warn';
                        elseif(in_array(strtolower($order->status), ['cancelled', 'blocked', 'failed'])) $statusClass = 'badge bad';
                    @endphp
                    <span class="{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                </p>
                <p style="margin-bottom:6px">Payment: 
                    @php
                        $payClass = 'badge';
                        if(strtolower($order->payment_status) == 'paid') $payClass = 'badge ok';
                        elseif(strtolower($order->payment_status) == 'pending') $payClass = 'badge warn';
                        elseif(strtolower($order->payment_status) == 'failed') $payClass = 'badge bad';
                    @endphp
                    <span class="{{ $payClass }}">{{ ucfirst($order->payment_status) }}</span>
                </p>
                <p style="margin-bottom:6px">Subtotal: ₹{{ number_format($order->subtotal, 0) }}</p>
                <p style="margin-bottom:6px">Shipping: ₹{{ number_format($order->shipping_fee, 0) }}</p>
                <p style="font-size:18px;margin-top:8px">Total: <strong style="color:var(--accent)">₹{{ number_format($order->total, 0) }}</strong></p>
            </div>
        </div>
    </section>

    <div class="head">
        <h2>Order Items</h2>
    </div>

    <section class="panel">
        <div class="scroll">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="pcell">
                                    @if($item->product && $item->product->primaryImage)
                                        <img src="{{ asset('storage/' . $item->product->primaryImage->image) }}" class="thumb" alt="{{ $item->product_name }}">
                                    @else
                                        <span class="thumb"></span>
                                    @endif
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        <div class="sub">{{ $item->vendor->vendorProfile->store_name ?? $item->vendor->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>₹{{ number_format($item->price, 0) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td style="text-align:right">₹{{ number_format($item->total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
