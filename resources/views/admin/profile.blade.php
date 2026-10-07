@extends('layouts.admin')

@section('title', 'My Profile')

@section('admin-content')
<div class="head">
  <div>
    <h1>My Profile</h1>
    <p>Manage your admin account settings</p>
  </div>
</div>

<div style="max-width:600px">
  <section class="panel" style="padding:24px">
    <form action="{{ route('admin.profile.update') }}" method="POST">
      @csrf
      @method('PUT')

      <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border)">Account Details</h3>

      <div class="field" style="margin-bottom:16px">
        <label for="name">Full Name</label>
        <input class="input" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="email">Email Address</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
      </div>

      <div class="field" style="margin-bottom:4px">
        <span style="font-size:12px;color:var(--muted);display:block;margin-bottom:2px">Role</span>
        <span class="badge ok">{{ ucfirst($user->role) }}</span>
      </div>

      <h3 style="font-size:15px;font-weight:600;margin:24px 0 16px;padding-bottom:12px;border-bottom:1px solid var(--border)">Change Password <span style="font-size:12px;font-weight:400;color:var(--muted)">(optional)</span></h3>

      <div class="field" style="margin-bottom:16px">
        <label for="current_password">Current Password</label>
        <input class="input" type="password" id="current_password" name="current_password" placeholder="Enter current password to change it">
      </div>

      <div class="field" style="margin-bottom:16px">
        <label for="password">New Password</label>
        <input class="input" type="password" id="password" name="password" placeholder="Min. 6 characters">
      </div>

      <div class="field" style="margin-bottom:24px">
        <label for="password_confirmation">Confirm New Password</label>
        <input class="input" type="password" id="password_confirmation" name="password_confirmation">
      </div>

      <div style="display:flex;gap:8px">
        <button type="submit" class="btn primary">Save Changes</button>
        <a class="btn" href="{{ route('admin.dashboard') }}">Cancel</a>
      </div>
    </form>
  </section>
</div>
@endsection
