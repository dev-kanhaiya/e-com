@extends('layouts.admin')

@section('title', 'Customer Details')

@section('admin-content')
<div class="head">
    <div>
        <h1>{{ $customer->name }}</h1>
        <p>Customer profile and order history</p>
    </div>
    <div style="display:flex;gap:8px">
        @if($customer->status === 'active')
            <form action="{{ route('admin.customers.block', $customer->id) }}" method="POST" onsubmit="return confirm('Block this customer?');" style="display:inline">
                @csrf
                <button type="submit" class="btn danger">Block Customer</button>
            </form>
        @else
            <form action="{{ route('admin.customers.unblock', $customer->id) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn primary">Unblock Customer</button>
            </form>
        @endif
        <a class="btn" href="{{ route('admin.customers.index') }}">Back to Customers</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:24px">
    <section class="panel" style="padding:16px">
        <h2 style="font-size:16px;margin-bottom:14px">Profile Information</h2>
        <div style="display:flex;flex-direction:column;gap:10px">
            <div><span style="font-size:13px;color:var(--muted);display:block">Full Name</span><strong>{{ $customer->name }}</strong></div>
            <div><span style="font-size:13px;color:var(--muted);display:block">Email</span>{{ $customer->email }}</div>
            <div>
                <span style="font-size:13px;color:var(--muted);display:block">Status</span>
                <span class="badge {{ $customer->status === 'active' ? 'ok' : 'bad' }}">{{ ucfirst($customer->status) }}</span>
            </div>
            <div><span style="font-size:13px;color:var(--muted);display:block">Registered At</span>{{ $customer->created_at->format('M d, Y h:i A') }}</div>
        </div>
    </section>

    <section class="panel" style="padding:16px">
        <h2 style="font-size:16px;margin-bottom:14px">Saved Addresses ({{ $customer->addresses->count() }})</h2>
        @forelse($customer->addresses as $addr)
            <div style="padding:10px;border:1px solid var(--border);border-radius:6px;margin-bottom:8px">
                <div style="display:flex;justify-content:space-between">
                    <strong>{{ $addr->name }} ({{ $addr->phone }})</strong>
                    @if($addr->is_default)
                        <span class="badge ok">Default</span>
                    @endif
                </div>
                <div style="color:var(--muted);font-size:13px;margin-top:4px">
                    {{ $addr->address }}, {{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}, {{ $addr->country }}
                </div>
            </div>
        @empty
            <p style="color:var(--muted);font-size:13px">No saved addresses for this customer.</p>
        @endforelse
    </section>
</div>

<section class="panel">
    <h2>Order History ({{ $customer->orders->count() }})</h2>
    <div class="scroll">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th class="act">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->orders as $order)
                    @php
                        $sBadge = match($order->status) { 'delivered' => 'ok', 'pending' => 'warn', 'cancelled' => 'bad', default => '' };
                        $pBadge = match($order->payment_status) { 'paid' => 'ok', 'pending' => 'warn', 'failed' => 'bad', default => '' };
                    @endphp
                    <tr>
                        <td><a class="link" href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                        <td>₹{{ number_format($order->total, 0) }}</td>
                        <td><span class="badge {{ $sBadge }}">{{ ucfirst($order->status) }}</span></td>
                        <td><span class="badge {{ $pBadge }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="act"><a class="btn sm" href="{{ route('admin.orders.show', $order->id) }}">Details</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--muted);padding:24px">No orders placed yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
