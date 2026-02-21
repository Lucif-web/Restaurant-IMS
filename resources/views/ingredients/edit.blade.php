@extends('layouts.app')
@section('title', 'Edit Ingredient')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-pencil me-2"></i>Edit: {{ $ingredient->name }}</div>
    <div class="card-body">
        <form action="{{ route('ingredients.update', $ingredient) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $ingredient->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                    @foreach(['kg','gram','piece','liter','ml'] as $u)
                    <option value="{{ $u }}" {{ old('unit', $ingredient->unit) == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                    @endforeach
                </select>
                @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold">Current Stock <span class="text-danger">*</span></label>
                    <input type="number" name="current_stock" step="0.001" min="0"
                           class="form-control @error('current_stock') is-invalid @enderror"
                           value="{{ old('current_stock', $ingredient->current_stock) }}" required>
                    @error('current_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">Minimum Stock <span class="text-danger">*</span></label>
                    <input type="number" name="minimum_stock" step="0.001" min="0"
                           class="form-control @error('minimum_stock') is-invalid @enderror"
                           value="{{ old('minimum_stock', $ingredient->minimum_stock) }}" required>
                    @error('minimum_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Update</button>
                <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <a href="{{ route('ingredients.stock-in', $ingredient) }}" class="btn btn-outline-success ms-auto">
                    <i class="bi bi-plus-circle me-1"></i>Add Stock
                </a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
