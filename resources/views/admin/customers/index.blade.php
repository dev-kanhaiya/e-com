@extends('layouts.admin')

@section('title', 'Customers')

@section('admin-content')
<div class="head">
    <div>
        <h1>Customers</h1>
        <p>Manage customer accounts and access</p>
    </div>
</div>

<form class="toolbar" method="GET" action="{{ route('admin.customers.index') }}">
    <div class="field grow">
        <label for="search">Search</label>
        <input class="input" id="search" name="search" placeholder="Search by name or email..." value="{{ request('search') }}">
    </div>
    <button class="btn primary" type="submit">Search</button>
    @if(request('search'))
        <a class="btn" href="{{ route('admin.customers.index') }}">Clear</a>
    @endif
</form>

<section class="panel">
    <div class="scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th class="act">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    @php
                        $badgeClass = $customer->status === 'active' ? 'ok' : 'bad';
                    @endphp
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td><a class="link" href="{{ route('admin.customers.show', $customer->id) }}">{{ $customer->name }}</a></td>
                        <td>{{ $customer->email }}</td>
                        <td><span class="badge {{ $badgeClass }}">{{ ucfirst($customer->status) }}</span></td>
                        <td>{{ $customer->orders_count ?? $customer->orders()->count() }}</td>
                        <td>{{ $customer->created_at->format('M d, Y') }}</td>
                        <td class="act">
                            <a class="btn sm" href="{{ route('admin.customers.show', $customer->id) }}">View</a>
                            @if($customer->status === 'active')
                                <form action="{{ route('admin.customers.block', $customer->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Block this customer?');">
                                    @csrf
                                    <button type="submit" class="btn sm danger">Block</button>
                                </form>
                            @else
                                <form action="{{ route('admin.customers.unblock', $customer->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn sm">Unblock</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if($customers->hasPages())
    <div style="padding:16px 0">
        {{ $customers->links() }}
    </div>
@endif
@endsection
