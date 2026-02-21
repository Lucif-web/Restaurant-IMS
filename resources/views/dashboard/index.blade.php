@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary fs-3"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="text-muted small">Total Orders</div>
                    <div class="fw-bold fs-4">{{ number_format($stats['total_orders']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning fs-3"><i class="bi bi-clock"></i></div>
                <div>
                    <div class="text-muted small">Pending Orders</div>
                    <div class="fw-bold fs-4">{{ number_format($stats['pending_orders']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success fs-3"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="text-muted small">Delivered Today</div>
                    <div class="fw-bold fs-4">{{ number_format($stats['delivered_today']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger fs-3"><i class="bi bi-currency-dollar"></i></div>
                <div>
                    <div class="text-muted small">Revenue Today</div>
                    <div class="fw-bold fs-4">${{ number_format($stats['revenue_today'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    @if($lowStockIngredients->isNotEmpty())
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-danger border-4">
            <div class="card-header bg-danger text-white fw-semibold">
                <i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alert — {{ $lowStockIngredients->count() }} ingredient(s) need restocking
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Ingredient</th><th>Current Stock</th><th>Minimum Stock</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    @foreach($lowStockIngredients as $ing)
                        <tr class="align-middle">
                            <td class="fw-semibold">{{ $ing->name }}</td>
                            <td><span class="text-danger fw-bold">{{ $ing->current_stock }} {{ $ing->unit }}</span></td>
                            <td>{{ $ing->minimum_stock }} {{ $ing->unit }}</td>
                            <td>
                                <a href="{{ route('ingredients.stock-in', $ing) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Add Stock
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span><i class="bi bi-receipt me-2"></i>Recent Orders</span>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Order #</th><th>Items</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr class="align-middle">
                            <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a></td>
                            <td>{{ $order->orderItems->count() }} item(s)</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $badge = match($order->status) {
                                        'pending'   => 'warning',
                                        'preparing' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'secondary',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No orders yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span><i class="bi bi-journal-text me-2"></i>Stock Activity</span>
                <a href="{{ route('stock-movements.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <ul class="list-group list-group-flush">
            @forelse($recentMovements as $mv)
                <li class="list-group-item d-flex align-items-center gap-2 py-2">
                    <span class="badge {{ $mv->type === 'deduction' ? 'bg-danger' : 'bg-success' }}">
                        {{ $mv->type === 'deduction' ? '−' : '+' }}
                    </span>
                    <div class="flex-grow-1">
                        <div class="small fw-semibold">{{ $mv->ingredient->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $mv->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="{{ $mv->type === 'deduction' ? 'text-danger' : 'text-success' }} fw-bold small">
                        {{ $mv->type === 'deduction' ? '-' : '+' }}{{ abs($mv->quantity) }}
                    </span>
                </li>
            @empty
                <li class="list-group-item text-center text-muted py-3">No stock activity.</li>
            @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
