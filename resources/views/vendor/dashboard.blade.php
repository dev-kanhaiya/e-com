@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('vendor-content')
<div class="head">
    <div>
        <h1>Vendor Dashboard</h1>
        <p>Overview of your store performance</p>
    </div>
</div>

<section class="stats">
    <div class="stat">
        <span>Total Products</span>
        <strong>{{ $totalProducts }}</strong>
    </div>
    <div class="stat">
        <span>Order Items Sold</span>
        <strong>{{ $totalOrderItems }}</strong>
    </div>
    <div class="stat">
        <span>Total Earnings</span>
        <strong>₹{{ number_format($totalEarnings, 0) }}</strong>
    </div>
</section>

<section class="panel">
    <h2>Recent Orders <a class="btn sm" href="{{ route('vendor.orders.index') }}">View All</a></h2>
    <div class="scroll">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Qty</th>
                    <th>Earnings</th>
                    <th>Date</th>
                    <th class="act">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrderItems as $item)
                    <tr>
                        <td><a class="link" href="{{ route('vendor.orders.show', $item->order_id) }}">{{ $item->order->order_number }}</a></td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->order->user->name ?? 'Customer' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td><strong>₹{{ number_format($item->total, 0) }}</strong></td>
                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                        <td class="act">
                            <a class="btn sm" href="{{ route('vendor.orders.show', $item->order_id) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--muted); padding: 24px;">No recent orders.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
