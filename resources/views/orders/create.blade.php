@extends('layouts.app')
@section('title', 'New Order')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-plus-circle me-2"></i>Create New Order</div>
    <div class="card-body">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">Notes (optional)</label>
                <input type="text" name="notes" class="form-control" placeholder="Table number, special requests..." value="{{ old('notes') }}">
            </div>

            <hr>
            <h6 class="fw-bold mb-3">Order Items</h6>

            <div id="orderItems">
                <div class="row g-2 mb-2 order-item-row">
                    <div class="col-7">
                        <select name="items[0][menu_item_id]" class="form-select item-select" required>
                            <option value="">— Select Menu Item —</option>
                            @foreach($menuItems as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                                    {{ $item->name }} ({{ $item->category->name }}) — ${{ number_format($item->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="number" name="items[0][quantity]" class="form-control item-qty"
                               value="1" min="1" required>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control item-subtotal bg-light" readonly placeholder="$0.00">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-outline-danger remove-row w-100" disabled>
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="addRow" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="bi bi-plus me-1"></i>Add Another Item
            </button>

            <div class="d-flex justify-content-end mt-3 gap-2 align-items-center">
                <span class="text-muted me-2">Total:</span>
                <span class="fs-5 fw-bold text-success" id="grandTotal">$0.00</span>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Place Order</button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
const menuItems = @json($menuItems->keyBy('id')->map(fn($i) => ['price' => $i->price, 'name' => $i->name]));
let rowCount = 1;

function updateRow(row) {
    const select = row.querySelector('.item-select');
    const qty = row.querySelector('.item-qty');
    const subtotal = row.querySelector('.item-subtotal');
    const selectedId = select.value;
    if (selectedId && menuItems[selectedId]) {
        const price = parseFloat(menuItems[selectedId].price);
        const q = parseInt(qty.value) || 1;
        subtotal.value = '$' + (price * q).toFixed(2);
    } else {
        subtotal.value = '';
    }
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.order-item-row').forEach(row => {
        const select = row.querySelector('.item-select');
        const qty = row.querySelector('.item-qty');
        if (select.value && menuItems[select.value]) {
            total += parseFloat(menuItems[select.value].price) * (parseInt(qty.value) || 1);
        }
    });
    document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
}

document.getElementById('orderItems').addEventListener('change', e => {
    const row = e.target.closest('.order-item-row');
    if (row) updateRow(row);
});

document.getElementById('orderItems').addEventListener('input', e => {
    if (e.target.classList.contains('item-qty')) {
        const row = e.target.closest('.order-item-row');
        if (row) updateRow(row);
    }
});

document.getElementById('addRow').addEventListener('click', () => {
    const i = rowCount++;
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 order-item-row';
    div.innerHTML = `
        <div class="col-7">
            <select name="items[${i}][menu_item_id]" class="form-select item-select" required>
                <option value="">— Select Menu Item —</option>
                ${Object.entries(menuItems).map(([id, item]) =>
                    `<option value="${id}" data-price="${item.price}">${item.name}</option>`
                ).join('')}
            </select>
        </div>
        <div class="col-2">
            <input type="number" name="items[${i}][quantity]" class="form-control item-qty" value="1" min="1" required>
        </div>
        <div class="col-2">
            <input type="text" class="form-control item-subtotal bg-light" readonly placeholder="$0.00">
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-outline-danger remove-row w-100"><i class="bi bi-x"></i></button>
        </div>`;
    document.getElementById('orderItems').appendChild(div);
    updateRemoveButtons();
});

document.getElementById('orderItems').addEventListener('click', e => {
    if (e.target.closest('.remove-row')) {
        e.target.closest('.order-item-row').remove();
        updateRemoveButtons();
        updateTotal();
    }
});

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.order-item-row');
    rows.forEach(r => {
        r.querySelector('.remove-row').disabled = rows.length === 1;
    });
}
</script>
@endpush
