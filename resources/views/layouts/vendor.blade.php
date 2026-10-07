<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'Vendor') – E-COM Vendor</title>
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
@stack('styles')
</head><body>

<header class="topbar"><div class="wrap">
  <a class="brand" href="{{ route('home') }}">E-<b>COM</b></a>
  <nav class="nav">
    <a href="{{ route('customer.products.index') }}">Products</a>
    <a class="on" href="{{ route('vendor.dashboard') }}">Vendor portal</a>
  </nav>
  <div class="userbox">
    <span class="name">{{ auth()->user()->name }}</span>
    <form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf
      <button type="submit" style="background:none;border:none;color:var(--bad);cursor:pointer;font:inherit;font-weight:500;padding:6px 10px;border-radius:6px">Logout</button>
    </form>
  </div>
</div></header>

<div class="admin">
  <nav class="side" aria-label="Vendor">
    <a class="{{ request()->routeIs('vendor.dashboard') ? 'on' : '' }}" href="{{ route('vendor.dashboard') }}">Dashboard</a>
    <a class="{{ request()->routeIs('vendor.products.*') ? 'on' : '' }}" href="{{ route('vendor.products.index') }}">My Products</a>
    <a class="{{ request()->routeIs('vendor.orders.*') ? 'on' : '' }}" href="{{ route('vendor.orders.index') }}">Customer Orders</a>
    <a class="{{ request()->routeIs('vendor.profile') ? 'on' : '' }}" href="{{ route('vendor.profile') }}" style="margin-top:8px;border-top:1px solid var(--border);padding-top:12px">Store Settings</a>
  </nav>
  <main>
    @if(session('success'))
      <div class="flash-msg" style="background:var(--ok-bg);color:var(--ok);padding:12px 16px;border-radius:8px;margin-bottom:16px">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="flash-msg" style="background:var(--bad-bg);color:var(--bad);padding:12px 16px;border-radius:8px;margin-bottom:16px">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="flash-msg" style="background:var(--bad-bg);color:var(--bad);padding:12px 16px;border-radius:8px;margin-bottom:16px">
        <ul style="margin:0;padding-left:20px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
    @yield('vendor-content')
  </main>
</div>

<script src="{{ asset('js/theme.js') }}"></script>
@stack('scripts')
</body></html>
