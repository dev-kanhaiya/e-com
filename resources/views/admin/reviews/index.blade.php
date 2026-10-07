@extends('layouts.admin')

@section('title', 'Customer Reviews')

@section('admin-content')
<div class="head">
    <div>
        <h1>Customer Reviews</h1>
        <p>Moderate customer ratings and feedbacks</p>
    </div>
</div>

<form class="toolbar" method="GET" action="{{ route('admin.reviews.index') }}">
    <div class="field grow">
        <label for="search">Search</label>
        <input class="input" id="search" name="search" placeholder="Search by customer, product, or comment..." value="{{ request('search') }}">
    </div>
    <button class="btn primary" type="submit">Search</button>
    @if(request('search'))
        <a class="btn" href="{{ route('admin.reviews.index') }}">Clear</a>
    @endif
</form>

<section class="panel">
    <div class="scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th class="act">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td>{{ $review->id }}</td>
                        <td><strong>{{ $review->product->name }}</strong></td>
                        <td>{{ $review->user->name }}</td>
                        <td style="color:var(--warn)">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </td>
                        <td>{{ Str::limit($review->comment, 80) }}</td>
                        <td>{{ $review->created_at->format('M d, Y') }}</td>
                        <td class="act">
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this review?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn sm danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No reviews found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if($reviews->hasPages())
    <div style="padding:16px 0">
        {{ $reviews->links() }}
    </div>
@endif
@endsection
