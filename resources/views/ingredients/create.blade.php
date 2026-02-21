@extends('layouts.app')
@section('title', 'Add Ingredient')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-2"></i>Add Ingredient</div>
    <div class="card-body">
        <form action="{{ route('ingredients.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. Tomato" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                    <option value="">— Select Unit —</option>
                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                    <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>Gram (gram)</option>
                    <option value="piece" {{ old('unit') == 'piece' ? 'selected' : '' }}>Piece</option>
                    <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter (L)</option>
                    <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                </select>
                @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold">Current Stock <span class="text-danger">*</span></label>
                    <input type="number" name="current_stock" step="0.001" min="0"
                           class="form-control @error('current_stock') is-invalid @enderror"
                           value="{{ old('current_stock', 0) }}" required>
                    @error('current_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">Minimum Stock <span class="text-danger">*</span></label>
                    <input type="number" name="minimum_stock" step="0.001" min="0"
                           class="form-control @error('minimum_stock') is-invalid @enderror"
                           value="{{ old('minimum_stock', 0) }}" required>
                    <div class="form-text">Alert when stock falls below this.</div>
                    @error('minimum_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Add Ingredient</button>
                <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
