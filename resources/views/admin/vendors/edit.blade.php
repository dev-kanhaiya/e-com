@extends('layouts.admin')

@section('title', 'Edit Vendor')

@section('admin-content')
<div class="head">
  <div>
    <h1>Edit Vendor: {{ $vendor->name }}</h1>
    <p>Update vendor details and store settings</p>
  </div>
  <a class="btn" href="{{ route('admin.vendors.index') }}">Back to Vendors</a>
</div>

<section class="panel" style="padding: 20px; max-width: 800px;">
  <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST">
    @csrf
    @method('PUT')

    <h3 style="margin-bottom: 14px; font-size: 16px;">Vendor Account</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
      <div class="field">
        <label for="name">Owner Full Name</label>
        <input class="input" type="text" id="name" name="name" value="{{ old('name', $vendor->name) }}" required>
      </div>
      <div class="field">
        <label for="email">Owner Email</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email', $vendor->email) }}" required>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
      <div class="field">
        <label for="status">Account Status</label>
        <select class="input" id="status" name="status" required>
          <option value="active" {{ old('status', $vendor->status) === 'active' ? 'selected' : '' }}>Active</option>
          <option value="blocked" {{ old('status', $vendor->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
      </div>
      <div class="field">
        <label for="profile_status">Store Approval Status</label>
        <select class="input" id="profile_status" name="profile_status" required>
          <option value="pending" {{ old('profile_status', $vendor->vendorProfile->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ old('profile_status', $vendor->vendorProfile->status ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
          <option value="rejected" {{ old('profile_status', $vendor->vendorProfile->status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
          <option value="blocked" {{ old('profile_status', $vendor->vendorProfile->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
      </div>
    </div>

    <h3 style="margin-bottom: 14px; font-size: 16px;">Store Profile</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
      <div class="field">
        <label for="store_name">Store Name</label>
        <input class="input" type="text" id="store_name" name="store_name" value="{{ old('store_name', $vendor->vendorProfile->store_name ?? '') }}" required>
      </div>
      <div class="field">
        <label for="phone">Store Phone</label>
        <input class="input" type="text" id="phone" name="phone" value="{{ old('phone', $vendor->vendorProfile->phone ?? '') }}">
      </div>
    </div>

    <div class="field" style="margin-bottom: 16px;">
      <label for="address">Store Address</label>
      <input class="input" type="text" id="address" name="address" value="{{ old('address', $vendor->vendorProfile->address ?? '') }}">
    </div>

    <div style="display: flex; gap: 10px; margin-top: 24px;">
      <button type="submit" class="btn primary">Update Vendor</button>
      <a class="btn" href="{{ route('admin.vendors.index') }}">Cancel</a>
    </div>
  </form>
</section>
@endsection
