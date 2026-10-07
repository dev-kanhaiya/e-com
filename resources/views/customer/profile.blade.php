@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="head">
  <div>
    <h1>My Profile</h1>
    <p>Manage your account settings</p>
  </div>
</div>

@if(session('success'))
  <div style="background:var(--ok-bg);color:var(--ok);padding:12px;border-radius:6px;margin-bottom:16px">
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div style="background:var(--bad-bg);color:var(--bad);padding:12px;border-radius:6px;margin-bottom:16px">
    <ul style="margin:0;padding-left:20px">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<section class="panel" style="padding:20px;max-width:600px">
  <form action="{{ route('customer.profile.update') }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="field" style="margin-bottom:16px">
      <label for="name">Name</label>
      <input class="input" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
    </div>
    
    <div class="field" style="margin-bottom:16px">
      <label for="email">Email address</label>
      <input class="input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>

    <h3 style="margin:24px 0 16px">Change Password (optional)</h3>
    <p style="color:var(--muted);margin-bottom:16px;font-size:14px">Leave blank if you do not want to change your password.</p>
    
    <div class="field" style="margin-bottom:16px">
      <label for="current_password">Current Password</label>
      <input class="input" type="password" id="current_password" name="current_password">
    </div>
    
    <div class="field" style="margin-bottom:16px">
      <label for="password">New Password</label>
      <input class="input" type="password" id="password" name="password">
    </div>
    
    <div class="field" style="margin-bottom:24px">
      <label for="password_confirmation">Confirm New Password</label>
      <input class="input" type="password" id="password_confirmation" name="password_confirmation">
    </div>

    <button type="submit" class="btn primary">Update Profile</button>
  </form>
</section>
@endsection
