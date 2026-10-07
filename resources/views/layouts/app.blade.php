<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'E-COM')</title>
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
@stack('styles')
</head><body>

<header class="topbar"><div class="wrap">
  <a class="brand" href="{{ route('home') }}">E-<b>COM</b></a>
  <nav class="nav">
    <a class="{{ request()->routeIs('customer.products.*') || request()->routeIs('home') ? 'on' : '' }}" href="{{ route('customer.products.index') }}">Products</a>
    @auth
      @if(auth()->user()->isCustomer())
        <a class="{{ request()->routeIs('customer.orders.*') ? 'on' : '' }}" href="{{ route('customer.orders.index') }}">Orders</a>
        <a class="{{ request()->routeIs('addresses.*') ? 'on' : '' }}" href="{{ route('addresses.index') }}">Addresses</a>
      @endif
    @endauth
  </nav>
  <div class="userbox">
    @auth
      @if(auth()->user()->isCustomer())
        <a href="{{ route('cart.index') }}" style="display:flex;align-items:center;gap:4px">
          Cart
          @php $cartCount = auth()->user()->cart ? auth()->user()->cart->items()->sum('quantity') : 0; @endphp
          @if($cartCount > 0)<span class="tag" style="padding:1px 6px;font-size:11px">{{ $cartCount }}</span>@endif
        </a>
        <a href="{{ route('customer.profile') }}" style="color:var(--muted);font-weight:500;padding:6px 10px;border-radius:6px">My Account</a>
      @endif

      @if(auth()->user()->isAdmin())
        <a class="btn primary sm" href="{{ route('admin.dashboard') }}">Dashboard</a>
      @elseif(auth()->user()->isVendor())
        <a class="btn primary sm" href="{{ route('vendor.dashboard') }}">Dashboard</a>
      @endif

      <span class="name">{{ auth()->user()->name }}</span>
      <form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf
        <button type="submit" style="background:none;border:none;color:var(--bad);cursor:pointer;font:inherit;font-weight:500;padding:6px 10px;border-radius:6px">Logout</button>
      </form>
    @else
      <a href="{{ route('login') }}">Login</a>
      <a class="btn primary sm" href="{{ route('register') }}">Register</a>
    @endauth
  </div>
</div></header>

<div class="wrap" style="padding-top:16px">
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
</div>

<main class="wrap">
  @yield('content')
</main>

<footer class="site-footer">
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <h4 style="font-size:16px;font-weight:700">E-<b>COM</b></h4>
        <p style="margin-top:8px">Your trusted multi-vendor e-commerce platform for quality products from verified local sellers across India.</p>
        <p style="margin-top:12px;line-height:1.6">
          <strong>Corporate Office:</strong><br>
          Plot No. 42, Ahinsa Khand II, Indirapuram,<br>
          Ghaziabad, Uttar Pradesh 201014<br>
          India
        </p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('customer.products.index') }}">All Products</a></li>
          @guest
            <li><a href="{{ route('login') }}">Sign In</a></li>
            <li><a href="{{ route('register') }}">Join as Vendor</a></li>
          @endguest
          @auth
            <li><a href="{{ route('customer.orders.index') }}">My Orders</a></li>
            <li><a href="{{ route('addresses.index') }}">Delivery Addresses</a></li>
          @endauth
        </ul>
      </div>
      <div>
        <h4>Customer Care</h4>
        <ul>
          <li><a href="mailto:support@e-com.in">Help Center</a></li>
          <li><a href="#">Shipping & Delivery Policy</a></li>
          <li><a href="#">Returns & Refund Policy</a></li>
          <li><a href="#">Terms & Conditions</a></li>
        </ul>
      </div>
      <div>
        <h4>Contact & Support</h4>
        <p>Support Email:<br><a href="mailto:support@e-com.in" style="color:var(--accent)">support@e-com.in</a></p>
        <p style="margin-top:8px">Helpline Number:<br><strong>+91 (0120) 456-7890</strong></p>
        <p style="margin-top:8px;font-size:12px;color:var(--muted)">Mon – Sat: 9:00 AM – 7:00 PM IST</p>
      </div>
    </div>
    <div class="footer-bottom">
      <div>&copy; {{ date('Y') }} E-COM India. All rights reserved.</div>
      <div>Secure checkout powered by Stripe &bull; Fast shipping across NCR</div>
    </div>
  </div>
</footer>

<script src="{{ asset('js/theme.js') }}"></script>
@stack('scripts')
</body></html>
