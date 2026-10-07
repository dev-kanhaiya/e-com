@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('admin-content')
<div class="head"><div><h1>Overview</h1><p>Welcome back, {{ auth()->user()->name }}</p></div></div>

<section class="stats">
  <div class="stat"><span>Customers</span><strong>{{ $totalCustomers }}</strong></div>
  <div class="stat"><span>Vendors</span><strong>{{ $totalVendors }}</strong></div>
  <div class="stat"><span>Products</span><strong>{{ $totalProducts }}</strong></div>
  <div class="stat"><span>Orders</span><strong>{{ $totalOrders }}</strong></div>
  <div class="stat"><span>Pending orders</span><strong>{{ $pendingOrders }}</strong></div>
  <div class="stat"><span>Total revenue</span><strong>₹{{ number_format($totalRevenue, 0) }}</strong></div>
</section>

<section class="panel">
  <h2>Recent orders <a class="btn sm" href="{{ route('admin.orders.index') }}">View all</a></h2>
  <div class="scroll"><table>
    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th class="act">Action</th></tr></thead>
    <tbody>
      @forelse($recentOrders as $order)
        @php
          $sBadge = match($order->status) { 'delivered' => 'ok', 'pending' => 'warn', 'cancelled' => 'bad', default => '' };
          $pBadge = match($order->payment_status) { 'paid' => 'ok', 'pending' => 'warn', 'failed' => 'bad', default => '' };
        @endphp
        <tr>
          <td><a class="link" href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
          <td>{{ $order->user->name }}</td>
          <td>₹{{ number_format($order->total, 0) }}</td>
          <td><span class="badge {{ $sBadge }}">{{ ucfirst($order->status) }}</span></td>
          <td><span class="badge {{ $pBadge }}">{{ ucfirst($order->payment_status) }}</span></td>
          <td>{{ $order->created_at->format('M d, Y') }}</td>
          <td class="act"><a class="btn sm" href="{{ route('admin.orders.show', $order->id) }}">Details</a></td>
        </tr>
      @empty
        <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No orders yet.</td></tr>
      @endforelse
    </tbody>
  </table></div>
</section>
@endsection
