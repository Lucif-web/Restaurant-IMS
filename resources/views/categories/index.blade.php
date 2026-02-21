@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-tags me-2"></i>Categories</h5>
    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus me-1"></i>New Category
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Menu Items</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr class="align-middle">
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td class="text-muted">{{ $category->description ?? '—' }}</td>
                    <td><span class="badge bg-secondary">{{ $category->menu_items_count }}</span></td>
                    <td class="text-muted small">{{ $category->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No categories yet. <a href="{{ route('categories.create') }}">Create one</a>.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
