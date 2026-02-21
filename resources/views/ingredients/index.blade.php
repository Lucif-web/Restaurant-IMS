@extends('layouts.app')
@section('title', 'Ingredients / Stock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Ingredients & Stock</h5>
    <a href="{{ route('ingredients.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i>Add Ingredient
    </a>
</div>

@if($lowStock->isNotEmpty())
<div class="alert alert-danger d-flex align-items-center">
    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
    <strong>{{ $lowStock->count() }} ingredient(s) are at or below minimum stock level!</strong>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Ingredient</th>
                    <th>Unit</th>
                    <th>Current Stock</th>
                    <th>Min. Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ingredients as $ingredient)
                <tr class="align-middle">
                    <td class="fw-semibold">{{ $ingredient->name }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $ingredient->unit }}</span></td>
                    <td>
                        <span class="{{ $ingredient->isLowStock() ? 'text-danger fw-bold' : '' }}">
                            {{ number_format($ingredient->current_stock, 2) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ number_format($ingredient->minimum_stock, 2) }}</td>
                    <td>
                        @if($ingredient->isLowStock())
                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock</span>
                        @else
                            <span class="badge bg-success">OK</span>
                        @endif
                    </td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('ingredients.stock-in', $ingredient) }}" class="btn btn-sm btn-outline-success" title="Add Stock">
                            <i class="bi bi-plus-circle"></i>
                        </a>
                        <a href="{{ route('ingredients.edit', $ingredient) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('ingredients.destroy', $ingredient) }}" method="POST"
                              onsubmit="return confirm('Delete this ingredient?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No ingredients yet. <a href="{{ route('ingredients.create') }}">Add one</a>.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($ingredients->hasPages())
    <div class="card-footer bg-white">{{ $ingredients->links() }}</div>
    @endif
</div>
@endsection
