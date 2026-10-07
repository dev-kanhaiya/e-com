@extends('layouts.admin')
@section('title', 'Categories')

@section('admin-content')
<div class="head">
  <div><h1>Categories</h1><p>Organize and manage product categories</p></div>
  <a class="btn primary" href="{{ route('admin.categories.create') }}">Add category</a>
</div>

<section class="panel"><div class="scroll"><table>
  <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Created</th><th class="act">Actions</th></tr></thead>
  <tbody>
    @forelse($categories as $category)
      <tr>
        <td>{{ $category->id }}</td>
        <td>{{ $category->name }}</td>
        <td><code>{{ $category->slug }}</code></td>
        <td>{{ $category->products_count ?? $category->products()->count() }}</td>
        <td><span class="badge {{ $category->status === 'active' ? 'ok' : 'bad' }}">{{ ucfirst($category->status) }}</span></td>
        <td>{{ $category->created_at->format('M d, Y') }}</td>
        <td class="act">
          <a class="btn sm" href="{{ route('admin.categories.edit', $category->id) }}">Edit</a>
          <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this category?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn sm danger">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:24px">No categories yet. Add your first category.</td></tr>
    @endforelse
  </tbody>
</table></div></section>

@if($categories->hasPages())
  <div style="padding:16px">{{ $categories->links() }}</div>
@endif
@endsection
