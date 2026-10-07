@extends('layouts.admin')

@section('title', 'Order Details')

@section('admin-content')
<div class="head">
    <div>
        <h1>Order {{ $order->order_number }}</h1>
        <p>Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
    </div>
    <a class="btn" href="{{ route('admin.orders.index') }}">Back to Orders</a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">
    <div>
        <section class="panel" style="margin-bottom:20px">
            <h2>Order Items</h2>
            <div class="scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Vendor</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th style="text-align:right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td>{{ $item->vendor->vendorProfile->store_name ?? $item->vendor->name }}</td>
                                <td>₹{{ number_format($item->price, 0) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td style="text-align:right">₹{{ number_format($item->total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:16px;border-top:1px solid var(--border);display:flex;flex-direction:column;align-items:flex-end;gap:6px">
                <div>Subtotal: <strong>₹{{ number_format($order->subtotal, 0) }}</strong></div>
                <div>Shipping: <strong>₹{{ number_format($order->shipping_fee, 0) }}</strong></div>
                <div style="font-size:18px;margin-top:4px">Grand Total: <strong style="color:var(--accent)">₹{{ number_format($order->total, 0) }}</strong></div>
            </div>
        </section>

        <section class="panel" style="padding:16px">
            <h2 style="font-size:16px;margin-bottom:12px">Customer & Delivery Information</h2>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <span style="font-size:13px;color:var(--muted);display:block">Customer</span>
                    <strong>{{ $order->user->name }}</strong><br>
                    <span style="font-size:13px;color:var(--muted)">{{ $order->user->email }}</span>
                </div>
                <div>
                    <span style="font-size:13px;color:var(--muted);display:block">Shipping Address</span>
                    <p style="white-space: pre-line;font-size:14px">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </section>
    </div>

    <div>
        <section class="panel" style="padding:16px;margin-bottom:20px">
            <h2 style="font-size:16px;margin-bottom:14px">Update Order Status</h2>
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="field" style="margin-bottom:14px">
                    <label for="status">Fulfillment Status</label>
                    <select class="input" id="status" name="status">
                        @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                            <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="margin-bottom:14px">
                    <label for="payment_status">Payment Status</label>
                    <select class="input" id="payment_status" name="payment_status">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <button type="submit" class="btn primary" style="width:100%">Save Changes</button>
            </form>
        </section>

        <section class="panel" style="padding:16px">
            <h2 style="font-size:16px;margin-bottom:12px">Payment Summary</h2>
            @if($order->payment)
                @php
                    $pBadge = match($order->payment->status) { 'paid' => 'ok', 'pending' => 'warn', default => 'bad' };
                @endphp
                <div style="display:flex;flex-direction:column;gap:8px;font-size:14px">
                    <div>Method: <strong>{{ strtoupper($order->payment->method) }}</strong></div>
                    <div>Status: <span class="badge {{ $pBadge }}">{{ ucfirst($order->payment->status) }}</span></div>
                    <div>Transaction ID: <code>{{ $order->payment->transaction_id ?? 'N/A' }}</code></div>
                    <div>Paid Date: {{ $order->payment->paid_at ? $order->payment->paid_at->format('M d, Y h:i A') : 'Pending' }}</div>
                </div>
            @else
                <p style="color:var(--muted);font-size:13px">No payment record found.</p>
            @endif
        </section>
    </div>
</div>
@endsection
