@extends('layouts.admin')
@section('title', 'Vendor Details')

@section('admin-content')
<div class="head">
  <div><h1>{{ $vendor->vendorProfile->store_name ?? $vendor->name }}</h1><p>Vendor account details</p></div>
  <a class="btn" href="{{ route('admin.vendors.index') }}">Back to vendors</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <section class="panel" style="padding:16px">
    <h2 style="font-size:16px;margin-bottom:16px">Account information</h2>
    <div style="display:flex;flex-direction:column;gap:12px">
      <div><span style="font-size:13px;color:var(--muted);display:block">Name</span>{{ $vendor->name }}</div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Email</span>{{ $vendor->email }}</div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Account status</span>
        <span class="badge {{ $vendor->status === 'active' ? 'ok' : 'bad' }}">{{ ucfirst($vendor->status) }}</span>
      </div>
      <div><span style="font-size:13px;color:var(--muted);display:block">Joined</span>{{ $vendor->created_at->format('M d, Y') }}</div>
    </div>
  </section>

  <section class="panel" style="padding:16px">
    <h2 style="font-size:16px;margin-bottom:16px">Store profile</h2>
    @if($vendor->vendorProfile)
      <div style="display:flex;flex-direction:column;gap:12px">
        <div><span style="font-size:13px;color:var(--muted);display:block">Store name</span>{{ $vendor->vendorProfile->store_name }}</div>
        <div><span style="font-size:13px;color:var(--muted);display:block">Description</span>{{ $vendor->vendorProfile->description ?? 'N/A' }}</div>
        <div><span style="font-size:13px;color:var(--muted);display:block">Phone</span>{{ $vendor->vendorProfile->phone ?? 'N/A' }}</div>
        <div><span style="font-size:13px;color:var(--muted);display:block">Address</span>{{ $vendor->vendorProfile->address ?? 'N/A' }}</div>
        <div><span style="font-size:13px;color:var(--muted);display:block">Approval status</span>
          @php $apprBadge = match($vendor->vendorProfile->status) { 'approved' => 'ok', 'pending' => 'warn', 'rejected' => 'bad', 'blocked' => 'bad', default => '' }; @endphp
          <span class="badge {{ $apprBadge }}">{{ ucfirst($vendor->vendorProfile->status) }}</span>
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-top:20px;flex-wrap:wrap">
        @if($vendor->vendorProfile->status === 'pending')
          <form action="{{ route('admin.vendors.approve', $vendor->id) }}" method="POST">@csrf
            <button type="submit" class="btn primary">Approve</button>
          </form>
          <form action="{{ route('admin.vendors.reject', $vendor->id) }}" method="POST">@csrf
            <button type="submit" class="btn danger">Reject</button>
          </form>
        @endif
        @if($vendor->status === 'active')
          <form action="{{ route('admin.vendors.block', $vendor->id) }}" method="POST" onsubmit="return confirm('Block this vendor?')">@csrf
            <button type="submit" class="btn danger">Block vendor</button>
          </form>
        @else
          <form action="{{ route('admin.vendors.unblock', $vendor->id) }}" method="POST">@csrf
            <button type="submit" class="btn">Unblock vendor</button>
          </form>
        @endif
      </div>
    @else
      <p style="color:var(--muted);font-size:13px">No store profile created yet.</p>
    @endif
  </section>
</div>
@endsection
