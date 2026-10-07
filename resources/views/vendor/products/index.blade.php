@extends('layouts.vendor')

@section('vendor-content')
<div class="wrap">
    <div class="head">
        <h1>My Products</h1>
        <a href="{{ route('vendor.products.create') }}" class="btn primary">Add New Product</a>
    </div>

    @if(session('success'))
        <div style="background: var(--ok-bg, #e6f4ea); color: var(--ok, #137333); padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    <section class="panel">
        <div class="scroll">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="act">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="pcell">
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image) }}" class="thumb" alt="{{ $product->name }}" loading="lazy" style="object-fit:cover">
                                    @elseif($product->images && $product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->image) }}" class="thumb" alt="{{ $product->name }}" loading="lazy" style="object-fit:cover">
                                    @else
                                        <div class="ph thumb"></div>
                                    @endif
                                    <div>
                                        <div class="name" style="font-weight:600">{{ $product->name }}</div>
                                        <div class="sub">{{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                            <td>₹{{ number_format($product->price, 0) }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                @php
                                    $statusClass = 'badge';
                                    if(strtolower($product->status) == 'active') $statusClass = 'badge ok';
                                    elseif(strtolower($product->status) == 'pending') $statusClass = 'badge warn';
                                    elseif(in_array(strtolower($product->status), ['blocked', 'inactive'])) $statusClass = 'badge bad';
                                @endphp
                                <span class="{{ $statusClass }}">{{ ucfirst($product->status) }}</span>
                            </td>
                            <td class="act">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn sm">Edit</a>
                                    <form action="{{ route('vendor.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn sm danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($products->hasPages())
        <div class="panel body" style="margin-top: 1.5rem;">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
