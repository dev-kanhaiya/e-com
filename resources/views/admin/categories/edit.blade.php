@extends('layouts.admin')
@section('title', 'Edit Category')

@section('admin-content')
<div class="head"><div><h1>Edit category</h1><p>Update category: {{ $category->name }}</p></div></div>

<section class="panel" style="padding:16px">
  <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="field" style="margin-bottom:16px">
      <label for="name">Category name</label>
      <input class="input" type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
    </div>
    <div class="field" style="margin-bottom:16px">
      <label for="description">Description</label>
      <textarea class="input" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
    </div>
    <div class="field" style="margin-bottom:16px">
      <label for="status">Status</label>
      <select class="input" id="status" name="status">
        <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
    <div style="display:flex;gap:8px;margin-top:16px">
      <button type="submit" class="btn primary">Save changes</button>
      <a class="btn" href="{{ route('admin.categories.index') }}">Cancel</a>
    </div>
  </form>
</section>
@endsection
