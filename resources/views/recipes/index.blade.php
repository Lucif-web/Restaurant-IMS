@extends('layouts.app')
@section('title', 'Recipe: ' . $menuItem->name)

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('menu-items.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="mb-0 fw-bold"><i class="bi bi-journal-code me-2"></i>Recipe: {{ $menuItem->name }}</h5>
    <span class="badge bg-secondary">{{ $menuItem->category->name }}</span>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-2"></i>Add Ingredient</div>
            <div class="card-body">
                <form action="{{ route('recipes.store', $menuItem) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ingredient <span class="text-danger">*</span></label>
                        <select name="ingredient_id" class="form-select @error('ingredient_id') is-invalid @enderror" required>
                            <option value="">— Select Ingredient —</option>
                            @foreach($availableIngredients as $ing)
                                @php $alreadyAdded = $menuItem->recipes->pluck('ingredient_id')->contains($ing->id); @endphp
                                <option value="{{ $ing->id }}" {{ $alreadyAdded ? 'disabled' : '' }} {{ old('ingredient_id') == $ing->id ? 'selected' : '' }}>
                                    {{ $ing->name }} ({{ $ing->unit }}){{ $alreadyAdded ? ' — already added' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('ingredient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantity per item <span class="text-danger">*</span></label>
                        <input type="number" name="quantity_required" step="0.001" min="0.001"
                               class="form-control @error('quantity_required') is-invalid @enderror"
                               value="{{ old('quantity_required') }}" placeholder="e.g. 50" required>
                        <div class="form-text">In the ingredient's unit (kg, gram, piece, etc.)</div>
                        @error('quantity_required')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus me-1"></i>Add to Recipe</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-list-check me-2"></i>Current Recipe
                <span class="badge bg-secondary ms-2">{{ $menuItem->recipes->count() }} ingredients</span>
            </div>
            @if($menuItem->recipes->isEmpty())
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted opacity-50"></i>
                    No ingredients in recipe yet. Add some from the left.
                </div>
            @else
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Ingredient</th><th>Unit</th><th>Qty Required / Item</th><th>Current Stock</th><th></th></tr>
                    </thead>
                    <tbody>
                    @foreach($menuItem->recipes as $recipe)
                        <tr class="align-middle">
                            <td class="fw-semibold">{{ $recipe->ingredient->name }}</td>
                            <td class="text-muted">{{ $recipe->ingredient->unit }}</td>
                            <td>
                                <form action="{{ route('recipes.update', [$menuItem, $recipe]) }}" method="POST" class="d-flex gap-1">
                                    @csrf @method('PUT')
                                    <input type="number" name="quantity_required" step="0.001" min="0.001"
                                           class="form-control form-control-sm" style="width:100px"
                                           value="{{ $recipe->quantity_required }}" required>
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Save">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                @php $isLow = $recipe->ingredient->current_stock <= $recipe->ingredient->minimum_stock; @endphp
                                <span class="{{ $isLow ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $recipe->ingredient->current_stock }} {{ $recipe->ingredient->unit }}
                                    @if($isLow) <i class="bi bi-exclamation-triangle ms-1"></i> @endif
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('recipes.destroy', [$menuItem, $recipe]) }}" method="POST"
                                      onsubmit="return confirm('Remove this ingredient from the recipe?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
