@extends('layouts.admin')

@section('title', 'Add New Vendor')

@section('admin-content')
<div class="head">
  <div>
    <h1>Add New Vendor</h1>
    <p>Create a vendor user account and store profile</p>
  </div>
  <a class="btn" href="{{ route('admin.vendors.index') }}">Back to Vendors</a>
</div>

<section class="panel" style="padding: 20px; max-width: 800px;">
  <form action="{{ route('admin.vendors.store') }}" method="POST">
    @csrf

    <h3 style="margin-bottom: 14px; font-size: 16px;">Vendor Account</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
      <div class="field">
        <label for="name">Owner Full Name</label>
        <input class="input" type="text" id="name" name="name" value="{{ old('name') }}" required>
      </div>
      <div class="field">
        <label for="email">Owner Email</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required>
      </div>
    </div>

    <div class="field" style="margin-bottom: 24px;">
      <label for="password">Password</label>
      <input class="input" type="password" id="password" name="password" required>
    </div>

    <h3 style="margin-bottom: 14px; font-size: 16px;">Store Profile</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
      <div class="field">
        <label for="store_name">Store Name</label>
        <input class="input" type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" required>
      </div>
      <div class="field">
        <label for="phone">Store Phone</label>
        <input class="input" type="text" id="phone" name="phone" value="{{ old('phone') }}">
      </div>
    </div>

    <div class="field" style="margin-bottom: 16px;">
      <label for="address">Store Address</label>
      <input class="input" type="text" id="address" name="address" value="{{ old('address') }}">
    </div>

    <div class="field" style="margin-bottom: 24px;">
      <label for="description">Store Description</label>
      <textarea class="input" id="description" name="description" rows="3">{{ old('description') }}</textarea>
    </div>

    <div style="display: flex; gap: 10px;">
      <button type="submit" class="btn primary">Create Vendor</button>
      <a class="btn" href="{{ route('admin.vendors.index') }}">Cancel</a>
    </div>
  </form>
</section>
@endsection
