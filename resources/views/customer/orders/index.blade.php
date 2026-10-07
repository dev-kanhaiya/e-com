@extends('layouts.app')

@section('content')
<div class="wrap">
    <div class="head">
        <h1>My Orders</h1>
    </div>

    @if(session('success'))
        <div style="background: var(--ok-bg, #e6f4ea); color: var(--ok, #137333); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    <section class="panel">
        <div class="scroll">
            <table>
                <thead>
                    <tr>
                        <th>Order#</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th class="act">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><a class="link" href="{{ route('customer.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                            <td>₹{{ number_format($order->total, 0) }}</td>
                            <td>
                                @php
                                    $statusClass = 'badge';
                                    if(in_array(strtolower($order->status), ['delivered', 'active', 'approved'])) $statusClass = 'badge ok';
                                    elseif(in_array(strtolower($order->status), ['pending'])) $statusClass = 'badge warn';
                                    elseif(in_array(strtolower($order->status), ['cancelled', 'blocked', 'failed'])) $statusClass = 'badge bad';
                                @endphp
                                <span class="{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>
                                @php
                                    $payClass = 'badge';
                                    if(strtolower($order->payment_status) == 'paid') $payClass = 'badge ok';
                                    elseif(strtolower($order->payment_status) == 'pending') $payClass = 'badge warn';
                                    elseif(strtolower($order->payment_status) == 'failed') $payClass = 'badge bad';
                                @endphp
                                <span class="{{ $payClass }}">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="act">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($orders->hasPages())
        <div class="panel body" style="margin-top: 1.5rem;">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
