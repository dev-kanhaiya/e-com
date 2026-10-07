@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div style="max-width:400px;margin:40px auto">
  <div class="head">
    <div>
      <h1>Register</h1>
      <p>Create an account to join E-COM</p>
    </div>
  </div>

  @if($errors->any())
    <div style="background:var(--bad-bg);color:var(--bad);padding:12px;border-radius:6px;margin-bottom:16px">
      <ul style="margin:0;padding-left:20px">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section class="panel" style="padding:20px">
    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="field" style="margin-bottom:16px">
        <label for="name">Name</label>
        <input class="input" type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label for="email">Email address</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label for="password">Password</label>
        <input class="input" type="password" id="password" name="password" required>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label for="password_confirmation">Confirm Password</label>
        <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label for="role">I am a...</label>
        <select class="input" id="role" name="role" required>
          <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
          <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
        </select>
      </div>
      <button type="submit" class="btn primary" style="width:100%">Register</button>
    </form>
  </section>
  <p style="text-align:center;margin-top:16px;color:var(--muted);font-size:14px">
    Already have an account? <a href="{{ route('login') }}" style="color:var(--accent);font-weight:500">Login</a>
  </p>
</div>
@endsection
