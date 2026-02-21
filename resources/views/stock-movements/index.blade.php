@extends('layouts.app')
@section('title', 'Stock Movement Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Stock Movement Logs</h5>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Ingredient</label>
                <select name="ingredient_id" class="form-select form-select-sm">
                    <option value="">All Ingredients</option>
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}" {{ request('ingredient_id') == $ing->id ? 'selected' : '' }}>
                            {{ $ing->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>Deduction</option>
                    <option value="manual_add" {{ request('type') == 'manual_add' ? 'selected' : '' }}>Manual Add</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date & Time</th>
                    <th>Ingredient</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Before</th>
                    <th>After</th>
                    <th>Order</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            @forelse($movements as $mv)
                <tr class="align-middle">
                    <td class="text-muted small">{{ $mv->created_at->format('d M Y, H:i') }}</td>
                    <td class="fw-semibold">{{ $mv->ingredient->name }}</td>
                    <td>
                        @if($mv->type === 'deduction')
                            <span class="badge bg-danger">Deduction</span>
                        @elseif($mv->type === 'manual_add')
                            <span class="badge bg-success">Manual Add</span>
                        @else
                            <span class="badge bg-secondary">Adjustment</span>
                        @endif
                    </td>
                    <td class="{{ $mv->quantity < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                        {{ $mv->quantity > 0 ? '+' : '' }}{{ $mv->quantity }} {{ $mv->ingredient->unit }}
                    </td>
                    <td class="text-muted">{{ $mv->stock_before }}</td>
                    <td class="text-muted">{{ $mv->stock_after }}</td>
                    <td>
                        @if($mv->order)
                            <a href="{{ route('orders.show', $mv->order) }}" class="small text-decoration-none">
                                {{ $mv->order->order_number }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $mv->notes ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No stock movements recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="card-footer bg-white">{{ $movements->links() }}</div>
    @endif
</div>
@endsection
