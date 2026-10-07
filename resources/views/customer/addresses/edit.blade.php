@extends('layouts.app')

@section('content')
<div class="wrap">
    <div class="head">
        <h1>Edit Address</h1>
        <a href="{{ route('addresses.index') }}" class="btn">Back</a>
    </div>

    @if($errors->any())
        <div style="background: var(--bad-bg, #fce8e6); color: var(--bad, #c5221f); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel body" style="max-width: 600px;">
        <form action="{{ route('addresses.update', $address->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" class="input" value="{{ old('name', $address->name) }}" required>
            </div>
            
            <div class="field">
                <label>Phone Number</label>
                <input type="text" name="phone" class="input" value="{{ old('phone', $address->phone) }}" required>
            </div>

            <div class="field">
                <label>Address Line</label>
                <input type="text" name="address" class="input" value="{{ old('address', $address->address) }}" required>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label>City</label>
                    <input type="text" name="city" class="input" value="{{ old('city', $address->city) }}" required>
                </div>
                <div class="field">
                    <label>State</label>
                    <input type="text" name="state" class="input" value="{{ old('state', $address->state) }}" required>
                </div>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="field">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" class="input" value="{{ old('postal_code', $address->postal_code) }}" required>
                </div>
                <div class="field">
                    <label>Country</label>
                    <input type="text" name="country" class="input" value="{{ old('country', $address->country) }}" required>
                </div>
            </div>

            <div class="field" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem;">
                <input type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                <label for="is_default" style="margin-bottom: 0;">Set as default address</label>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn primary">Update Address</button>
            </div>
        </form>
    </section>
</div>
@endsection
