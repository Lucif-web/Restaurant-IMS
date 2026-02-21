@extends('layouts.app')
@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Orders</h5>
    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i>New Order
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                @php
                    $badge = match($order->status) {
                        'pending'   => 'warning',
                        'preparing' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'secondary',
                        default     => 'secondary',
                    };
                @endphp
                <tr class="align-middle">
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="fw-bold text-decoration-none">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td>{{ $order->orderItems->count() }} item(s)</td>
                    <td class="fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                    <td><span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(!in_array($order->status, ['delivered', 'cancelled']))
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this order?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No orders yet. <a href="{{ route('orders.create') }}">Create one</a>.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
