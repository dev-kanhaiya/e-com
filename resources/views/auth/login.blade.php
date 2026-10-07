@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div style="max-width:400px;margin:40px auto">
  <div class="head">
    <div>
      <h1>Login</h1>
      <p>Welcome back to E-COM</p>
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
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="field" style="margin-bottom:16px">
        <label for="email">Email address</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="field" style="margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
          <label for="password" style="margin-bottom:0">Password</label>
          <a href="{{ route('password.request') }}" style="font-size:12px;color:var(--accent);font-weight:500">Forgot password?</a>
        </div>
        <input class="input" type="password" id="password" name="password" required>
      </div>
      <div style="margin-bottom:16px">
        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted)">
          <input type="checkbox" name="remember"> Remember me
        </label>
      </div>
      <button type="submit" class="btn primary" style="width:100%">Login</button>
    </form>
  </section>
  <p style="text-align:center;margin-top:16px;color:var(--muted);font-size:14px">
    Don't have an account? <a href="{{ route('register') }}" style="color:var(--accent);font-weight:500">Register</a>
  </p>
</div>
@endsection
