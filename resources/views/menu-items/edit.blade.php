@extends('layouts.app')
@section('title', 'Edit Menu Item')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-pencil me-2"></i>Edit: {{ $menuItem->name }}</div>
    <div class="card-body">
        <form action="{{ route('menu-items.update', $menuItem) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $menuItem->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Price (Rs) <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0"
                           class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $menuItem->price) }}" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $menuItem->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Image</label>
                    @if($menuItem->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$menuItem->image) }}" height="50" class="rounded border">
                            <small class="text-muted ms-2">Current image</small>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $menuItem->description) }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_available" id="isAvailable"
                               value="1" {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isAvailable">Available for ordering</label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Update Item</button>
                <a href="{{ route('menu-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <a href="{{ route('recipes.index', $menuItem) }}" class="btn btn-outline-info ms-auto">
                    <i class="bi bi-journal-code me-1"></i>Manage Recipe
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
