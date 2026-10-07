@extends('layouts.admin')

@section('title', 'All Orders')

@section('admin-content')
<div class="head">
    <div>
        <h1>Orders</h1>
        <p>Manage, track, and update all customer orders</p>
    </div>
    <a class="btn primary sm" href="{{ route('admin.orders.export', request()->query()) }}">
        Download Report (CSV)
    </a>
</div>

<form class="toolbar" method="GET" action="{{ route('admin.orders.index') }}">
    <div class="field grow">
        <label for="search">Search</label>
        <input class="input" id="search" name="search" placeholder="Search by order number or customer name..." value="{{ request('search') }}">
    </div>
    <div class="field">
        <label for="status">Order Status</label>
        <select class="input" id="status" name="status">
            <option value="">All Statuses</option>
            @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="payment_status">Payment Status</label>
        <select class="input" id="payment_status" name="payment_status">
            <option value="">All Payments</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>
    </div>
    <button class="btn primary" type="submit">Filter</button>
    <a class="btn" href="{{ route('admin.orders.index') }}">Reset</a>
</form>

<section class="panel">
    <div class="scroll">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th class="act">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $sBadge = match($order->status) { 'delivered' => 'ok', 'pending' => 'warn', 'cancelled' => 'bad', default => '' };
                        $pBadge = match($order->payment_status) { 'paid' => 'ok', 'pending' => 'warn', 'failed' => 'bad', default => '' };
                    @endphp
                    <tr>
                        <td><a class="link" href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td><strong>₹{{ number_format($order->total, 0) }}</strong></td>
                        <td><span class="badge {{ $sBadge }}">{{ ucfirst($order->status) }}</span></td>
                        <td><span class="badge {{ $pBadge }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="act">
                            <a class="btn sm" href="{{ route('admin.orders.show', $order->id) }}">Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--muted);padding:24px">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if($orders->hasPages())
    <div style="padding:16px 0">
        {{ $orders->links() }}
    </div>
@endif
@endsection
