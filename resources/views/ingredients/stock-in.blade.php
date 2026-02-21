@extends('layouts.app')
@section('title', 'Stock In: ' . $ingredient->name)

@section('content')
<div class="row justify-content-center">
<div class="col-lg-5">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-plus-circle me-2 text-success"></i>Add Stock: {{ $ingredient->name }}
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Current Stock:</strong> {{ $ingredient->current_stock }} {{ $ingredient->unit }}
            &nbsp;|&nbsp;
            <strong>Minimum:</strong> {{ $ingredient->minimum_stock }} {{ $ingredient->unit }}
        </div>
        <form action="{{ route('ingredients.add-stock', $ingredient) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Quantity to Add ({{ $ingredient->unit }}) <span class="text-danger">*</span></label>
                <input type="number" name="quantity" step="0.001" min="0.001"
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" placeholder="e.g. 5" required autofocus>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Notes</label>
                <input type="text" name="notes" class="form-control"
                       value="{{ old('notes') }}" placeholder="e.g. Purchased from supplier">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-plus me-1"></i>Add to Stock</button>
                <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
