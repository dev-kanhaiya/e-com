@extends('layouts.app')
@section('title', 'Forgot Password')

@section('content')
<div style="max-width:440px;margin:40px auto">
  <div class="head">
    <div>
      <h1>Forgot Password</h1>
      <p>Enter your registered email address to receive a new password.</p>
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

  <section class="panel" style="padding:24px">
    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="field" style="margin-bottom:20px">
        <label for="email">Registered Email Address</label>
        <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
      </div>

      <button type="submit" class="btn primary" style="width:100%;padding:10px">Send New Password</button>
    </form>
  </section>

  <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--muted)">
    Remember your password? <a href="{{ route('login') }}" style="color:var(--accent);font-weight:600">Back to Login</a>
  </p>
</div>
@endsection
