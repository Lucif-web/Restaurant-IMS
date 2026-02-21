@extends('layouts.app')
@section('title', 'Menu Items')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-card-list me-2"></i>Menu Items</h5>
    <a href="{{ route('menu-items.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i>Add Menu Item
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Recipe</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($menuItems as $item)
                <tr class="align-middle">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($item->image)
                                <img src="{{ asset('storage/'.$item->image) }}" class="rounded" width="40" height="40" style="object-fit:cover">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                <div class="text-muted small">{{ Str::limit($item->description, 40) }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-secondary bg-opacity-75">{{ $item->category->name }}</span></td>
                    <td class="fw-semibold">${{ number_format($item->price, 2) }}</td>
                    <td>
                        <a href="{{ route('recipes.index', $item) }}" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-journal-code me-1"></i>Recipe
                        </a>
                    </td>
                    <td>
                        @if($item->is_available)
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-secondary">Unavailable</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('menu-items.edit', $item) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('menu-items.destroy', $item) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this menu item?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No menu items yet. <a href="{{ route('menu-items.create') }}">Add one</a>.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($menuItems->hasPages())
    <div class="card-footer bg-white">{{ $menuItems->links() }}</div>
    @endif
</div>
@endsection
