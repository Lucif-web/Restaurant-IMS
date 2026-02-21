@extends('layouts.app')
@section('title', 'Order: ' . $order->order_number)

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="mb-0 fw-bold">{{ $order->order_number }}</h5>
    @php
        $badge = match($order->status) {
            'pending'   => 'warning',
            'preparing' => 'info',
            'delivered' => 'success',
            'cancelled' => 'secondary',
            default     => 'secondary',
        };
    @endphp
    <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($order->status) }}</span>
</div>

<div class="row g-3">
    {{-- Order Items --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-list me-2"></i>Order Items</div>
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th>Item</th><th class="text-center">Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->menuItem->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th>${{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Stock Deductions (if delivered) --}}
        @if($order->stockMovements->isNotEmpty())
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-arrow-down-circle me-2 text-danger"></i>Stock Deducted</div>
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>Ingredient</th><th>Deducted</th><th>Stock After</th></tr></thead>
                <tbody>
                @foreach($order->stockMovements as $mv)
                <tr>
                    <td>{{ $mv->ingredient->name }}</td>
                    <td class="text-danger fw-semibold">{{ abs($mv->quantity) }} {{ $mv->ingredient->unit }}</td>
                    <td class="text-muted">{{ $mv->stock_after }} {{ $mv->ingredient->unit }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Status & Actions --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-gear me-2"></i>Order Actions</div>
            <div class="card-body">
                <p class="text-muted small">Placed: {{ $order->created_at->format('d M Y, H:i') }}</p>
                @if($order->delivered_at)
                    <p class="text-muted small">Delivered: {{ $order->delivered_at->format('d M Y, H:i') }}</p>
                @endif
                @if($order->notes)
                    <p><strong>Notes:</strong> {{ $order->notes }}</p>
                @endif

                @if(!in_array($order->status, ['delivered', 'cancelled']))
                <hr>
                <p class="fw-semibold small mb-2">Update Status:</p>
                <div class="d-flex flex-wrap gap-2">
                    @if($order->status === 'pending')
                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="preparing">
                        <button class="btn btn-info btn-sm"><i class="bi bi-fire me-1"></i>Mark Preparing</button>
                    </form>
                    @endif

                    @if(in_array($order->status, ['pending', 'preparing']))
                    <form action="{{ route('orders.update-status', $order) }}" method="POST"
                          onsubmit="return confirm('Mark as DELIVERED? This will automatically deduct ingredient stock!')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="delivered">
                        <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Mark Delivered</button>
                    </form>

                    <form action="{{ route('orders.update-status', $order) }}" method="POST"
                          onsubmit="return confirm('Cancel this order?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Cancel Order</button>
                    </form>
                    @endif
                </div>

                {{-- Stock preview --}}
                <div class="mt-3 p-3 bg-light rounded">
                    <p class="small fw-semibold mb-2"><i class="bi bi-info-circle me-1"></i>Stock Impact (on delivery):</p>
                    @foreach($order->orderItems as $oi)
                        @foreach($oi->menuItem->recipes as $recipe)
                        <div class="d-flex justify-content-between small text-muted">
                            <span>{{ $recipe->ingredient->name }}</span>
                            <span class="text-danger">−{{ $recipe->quantity_required * $oi->quantity }} {{ $recipe->ingredient->unit }}</span>
                        </div>
                        @endforeach
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
