@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="head">
  <div>
    <h1>Checkout</h1>
    <p>Complete your purchase securely.</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:32px">
  <div>
    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
      @csrf

      <section class="panel" style="padding:20px;margin-bottom:24px">
        <h3 style="margin-bottom:14px">1. Select Delivery Address</h3>
        
        @if($addresses->count() > 0)
          <div style="display:flex;flex-direction:column;gap:12px">
            @foreach($addresses as $address)
              <label style="display:flex;align-items:flex-start;gap:12px;padding:12px;border:1px solid var(--border);border-radius:6px;cursor:pointer">
                <input type="radio" name="address_id" value="{{ $address->id }}" {{ ($address->is_default || $loop->first) ? 'checked' : '' }} style="margin-top:4px">
                <div>
                  <strong>{{ $address->name }} ({{ $address->phone }})</strong>
                  @if($address->is_default)
                    <span class="badge ok">Default</span>
                  @endif
                  <div style="color:var(--muted);font-size:13px;margin-top:4px">
                    {{ $address->address }}, {{ $address->city }}, {{ $address->state }} - {{ $address->postal_code }}, {{ $address->country }}
                  </div>
                </div>
              </label>
            @endforeach
          </div>
          <div style="margin-top:12px">
            <a href="{{ route('addresses.create') }}" class="btn sm">+ Add New Address</a>
          </div>
        @else
          <p style="color:var(--muted);margin-bottom:12px">You have no saved addresses.</p>
          <a href="{{ route('addresses.create') }}" class="btn primary sm">Add Delivery Address First</a>
        @endif
      </section>

      <section class="panel" style="padding:20px;margin-bottom:24px">
        <h3 style="margin-bottom:14px">2. Select Payment Method</h3>
        
        <div style="display:flex;flex-direction:column;gap:12px">
          <label style="display:flex;align-items:center;gap:10px;padding:12px;border:1px solid var(--border);border-radius:6px;cursor:pointer">
            <input type="radio" name="payment_method" value="stripe" checked id="pay-stripe">
            <strong>Credit / Debit Card (Stripe Test Mode)</strong>
          </label>
          <label style="display:flex;align-items:center;gap:10px;padding:12px;border:1px solid var(--border);border-radius:6px;cursor:pointer">
            <input type="radio" name="payment_method" value="cod" id="pay-cod">
            <strong>Cash on Delivery (COD)</strong>
          </label>
        </div>

        <div id="stripe-card-section" style="margin-top:16px;padding:16px;background:var(--bg);border:1px solid var(--border);border-radius:6px">
          <label for="card-element" style="display:block;font-size:13px;font-weight:500;margin-bottom:8px">Card Information (Stripe)</label>
          <div id="card-element" class="input" style="padding:10px;background:#fff">
            <!-- Stripe Element will be mounted here -->
          </div>
          <div id="card-errors" role="alert" style="color:var(--bad);font-size:13px;margin-top:8px"></div>
        </div>

        <input type="hidden" name="stripe_token" id="stripe_token">

        @if($addresses->count() > 0)
          <div style="margin-top:20px">
            <button type="submit" class="btn primary" id="submit-button" style="width:100%;padding:12px;font-size:16px">
              Place Order (₹{{ number_format($total, 0) }})
            </button>
          </div>
        @endif
      </section>
    </form>
  </div>

  <div>
    <section class="panel" style="padding:20px;position:sticky;top:80px">
      <h3 style="margin-bottom:16px">Order Summary</h3>
      
      <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px">
        @foreach($cart->items as $item)
          <div style="display:flex;justify-content:space-between;font-size:14px">
            <span>{{ $item->product->name }} <span style="color:var(--muted)">× {{ $item->quantity }}</span></span>
            <strong>₹{{ number_format($item->price * $item->quantity, 0) }}</strong>
          </div>
        @endforeach
      </div>
      
      <div style="border-top:1px solid var(--border);padding-top:12px;margin-bottom:12px;display:flex;flex-direction:column;gap:6px">
        <div style="display:flex;justify-content:space-between;color:var(--muted)">
          <span>Subtotal</span>
          <span>₹{{ number_format($subtotal, 0) }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;color:var(--muted)">
          <span>Shipping Fee</span>
          <span>₹{{ number_format($shipping, 0) }}</span>
        </div>
      </div>
      
      <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;justify-content:space-between;font-size:18px">
        <span>Total Amount</span>
        <strong style="color:var(--accent)">₹{{ number_format($total, 0) }}</strong>
      </div>
    </section>
  </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
  var stripeKey = '{{ env("STRIPE_KEY") }}';
  var stripe = Stripe(stripeKey);
  var elements = stripe.elements();
  var card = elements.create('card');
  card.mount('#card-element');

  card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = '';
    }
  });

  var stripeSection = document.getElementById('stripe-card-section');
  document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      if (this.value === 'stripe') {
        stripeSection.style.display = 'block';
      } else {
        stripeSection.style.display = 'none';
      }
    });
  });

  var form = document.getElementById('checkout-form');
  form.addEventListener('submit', function(event) {
    var selectedAddress = document.querySelector('input[name="address_id"]:checked');
    if (!selectedAddress) {
      event.preventDefault();
      alert('Please select a delivery address before placing order.');
      return;
    }

    var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    var paymentMethod = selectedPayment ? selectedPayment.value : 'stripe';

    if (paymentMethod === 'stripe') {
      event.preventDefault();
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = '';

      var submitBtn = document.getElementById('submit-button');
      submitBtn.disabled = true;

      if (window.showAppLoader) {
        window.showAppLoader("Verifying card & processing payment...");
      }

      stripe.createPaymentMethod({
        type: 'card',
        card: card,
      }).then(function(result) {
        if (result.error) {
          if (window.hideAppLoader) {
            window.hideAppLoader();
          }
          errorElement.textContent = result.error.message;
          submitBtn.disabled = false;
        } else {
          if (window.showAppLoader) {
            window.showAppLoader("Placing your order...");
          }
          document.getElementById('stripe_token').value = result.paymentMethod.id;
          form.submit();
        }
      }).catch(function(err) {
        if (window.hideAppLoader) {
          window.hideAppLoader();
        }
        errorElement.textContent = err.message || 'Payment processing error. Please try again.';
        submitBtn.disabled = false;
      });
    } else {
      if (window.showAppLoader) {
        window.showAppLoader("Placing your order...");
      }
    }
  });
</script>
@endsection
