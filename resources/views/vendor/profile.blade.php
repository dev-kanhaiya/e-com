@extends('layouts.vendor')

@section('vendor-content')
<div class="head">
  <div>
    <h1>Store Settings</h1>
    <p>Manage your store details and account credentials</p>
  </div>
</div>

<div style="max-width:640px">
  <section class="panel" style="padding:24px">
    <form action="{{ route('vendor.profile.update') }}" method="POST">
      @csrf
      @method('PUT')

      @php
        $profile = isset($profile) ? $profile : auth()->user()->vendorProfile;
      @endphp

      {{-- ─── Account Details ─────────────────────────── --}}
      <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)">Account Details</h3>

      <div class="field" style="margin-bottom:16px">
        <label for="name">Full Name</label>
        <input class="input" type="text" id="name" name="name" value="{{ old('name', $vendor->name) }}" required>
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="email">Email Address</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email', $vendor->email) }}" required>
      </div>

      {{-- ─── Change Password ──────────────────────────── --}}
      <h3 style="font-size:15px;font-weight:600;margin:24px 0 16px;padding-bottom:12px;border-bottom:1px solid var(--border)">Change Password <span style="font-size:12px;font-weight:400;color:var(--muted)">(optional)</span></h3>

      <div class="field" style="margin-bottom:16px">
        <label for="current_password">Current Password</label>
        <input class="input" type="password" id="current_password" name="current_password" placeholder="Enter current password to change">
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="password">New Password</label>
        <input class="input" type="password" id="password" name="password" placeholder="Min. 6 characters">
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="password_confirmation">Confirm New Password</label>
        <input class="input" type="password" id="password_confirmation" name="password_confirmation">
      </div>

      {{-- ─── Store Info ───────────────────────────────── --}}
      <h3 style="font-size:15px;font-weight:600;margin:24px 0 16px;padding-bottom:12px;border-bottom:1px solid var(--border)">Store Information</h3>

      <div class="field" style="margin-bottom:16px">
        <label for="store_name">Store Name</label>
        <input class="input" type="text" id="store_name" name="store_name" value="{{ old('store_name', optional($profile)->store_name) }}" required>
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="description">Store Description</label>
        <textarea class="input" id="description" name="description" rows="3" placeholder="Tell customers about your store...">{{ old('description', optional($profile)->description) }}</textarea>
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="phone">Contact Phone</label>
        <input class="input" type="text" id="phone" name="phone" value="{{ old('phone', optional($profile)->phone) }}" placeholder="+91 98765 43210">
      </div>

      <div class="field" style="margin-bottom:24px">
        <label for="address">Store Address</label>
        <input class="input" type="text" id="address" name="address" value="{{ old('address', optional($profile)->address) }}" placeholder="Full store address">
      </div>

      <div style="display:flex;gap:8px">
        <button type="submit" class="btn primary">Save All Settings</button>
        <a class="btn" href="{{ route('vendor.dashboard') }}">Cancel</a>
      </div>
    </form>
  </section>
</div>
@endsection
