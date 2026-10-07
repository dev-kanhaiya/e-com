@extends('layouts.app')

@section('content')
<div class="wrap">
    <div class="head">
        <h1>My Addresses</h1>
        <a href="{{ route('addresses.create') }}" class="btn primary">Add New Address</a>
    </div>

    @if(session('success'))
        <div style="background: var(--ok-bg, #e6f4ea); color: var(--ok, #137333); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        @forelse($addresses as $address)
            <div class="card panel body" style="position: relative;">
                @if($address->is_default)
                    <span class="badge ok" style="position: absolute; top: 1rem; right: 1rem;">Default</span>
                @endif
                <h3>{{ $address->name }}</h3>
                <p>{{ $address->phone }}</p>
                <p>{{ $address->address }}</p>
                <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                <p>{{ $address->country }}</p>
                <div class="toolbar" style="margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: flex-start;">
                    <a href="{{ route('addresses.edit', $address->id) }}" class="btn sm">Edit</a>
                    <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Delete this address?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn sm danger">Delete</button>
                    </form>
                    @if(!$address->is_default)
                        <form action="{{ route('addresses.default', $address->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn sm primary">Set Default</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="panel body" style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                <p>No addresses found.</p>
                <a href="{{ route('addresses.create') }}" class="btn primary" style="margin-top: 1rem; display: inline-block;">Add New Address</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
