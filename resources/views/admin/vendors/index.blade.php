@extends('layouts.admin')
@section('title', 'Vendors')

@section('admin-content')
<div class="head">
  <div>
    <h1>Vendors</h1>
    <p>Approve, reject, create or manage vendor accounts</p>
  </div>
  <a class="btn primary" href="{{ route('admin.vendors.create') }}">Add Vendor</a>
</div>

<form class="toolbar" method="GET" action="{{ route('admin.vendors.index') }}">
  <div class="field grow"><label for="search">Search</label><input class="input" id="search" name="search" placeholder="Search by name, email, or store" value="{{ request('search') }}"></div>
  <button class="btn primary" type="submit">Search</button>
  @if(request('search'))
    <a class="btn" href="{{ route('admin.vendors.index') }}">Clear</a>
  @endif
</form>

<section class="panel"><div class="scroll"><table>
  <thead><tr><th>ID</th><th>Store</th><th>Owner</th><th>Email</th><th>Account</th><th>Approval</th><th class="act">Actions</th></tr></thead>
  <tbody>
    @forelse($vendors as $vendor)
      @php
        $accBadge = match($vendor->status) { 'active' => 'ok', 'blocked' => 'bad', default => '' };
        $profile = $vendor->vendorProfile;
        $apprBadge = match($profile->status ?? '') { 'approved' => 'ok', 'pending' => 'warn', 'rejected' => 'bad', 'blocked' => 'bad', default => '' };
      @endphp
      <tr>
        <td>{{ $vendor->id }}</td>
        <td><a class="link" href="{{ route('admin.vendors.show', $vendor->id) }}">{{ $profile->store_name ?? 'N/A' }}</a></td>
        <td>{{ $vendor->name }}</td>
        <td>{{ $vendor->email }}</td>
        <td><span class="badge {{ $accBadge }}">{{ ucfirst($vendor->status) }}</span></td>
        <td><span class="badge {{ $apprBadge }}">{{ ucfirst($profile->status ?? 'N/A') }}</span></td>
        <td class="act">
          <a class="btn sm" href="{{ route('admin.vendors.show', $vendor->id) }}">View</a>
          <a class="btn sm" href="{{ route('admin.vendors.edit', $vendor->id) }}">Edit</a>
          @if($vendor->status === 'active')
            <form action="{{ route('admin.vendors.block', $vendor->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Block this vendor?')">
              @csrf
              <button type="submit" class="btn sm danger">Block</button>
            </form>
          @else
            <form action="{{ route('admin.vendors.unblock', $vendor->id) }}" method="POST" style="display:inline">
              @csrf
              <button type="submit" class="btn sm">Unblock</button>
            </form>
          @endif
          <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this vendor?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn sm danger">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No vendors registered yet.</td></tr>
    @endforelse
  </tbody>
</table></div></section>

@if($vendors->hasPages())
  <div style="padding:16px">{{ $vendors->links() }}</div>
@endif
@endsection
