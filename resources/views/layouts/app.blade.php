<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Restaurant IMS') — RestaurantIMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #e63946;
            --dark: #1d3557;
        }

        body {
            background: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--dark);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar .brand {
            padding: 1.5rem 1.25rem 1rem;
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .brand span {
            color: var(--primary);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: .65rem 1.25rem;
            border-radius: 0;
            transition: all .2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--primary);
        }

        .sidebar .nav-section {
            color: rgba(255, 255, 255, 0.4);
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 1rem 1.25rem .25rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: .75rem 1.5rem;
        }

        .no-style {
            text-decoration: none;
            color: inherit;
        }

        .page-body {
            padding: 1.5rem;
        }

        .badge-low {
            background: #ff6b6b;
        }

        .stock-bar {
            height: 6px;
            border-radius: 3px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <nav class="sidebar">
        <div class="brand"><a href="{{ route('dashboard') }}" class="no-style">🍽️ <span>Restaurant</span>IMS</a></div>
        <ul class="nav flex-column pt-2">
            <li><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a></li>

            <li class="nav-section">Menu</li>
            <li><a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                    href="{{ route('categories.index') }}">
                    <i class="bi bi-tags me-2"></i>Categories
                </a></li>
            <li><a class="nav-link {{ request()->routeIs('menu-items.*') ? 'active' : '' }}"
                    href="{{ route('menu-items.index') }}">
                    <i class="bi bi-card-list me-2"></i>Menu Items
                </a></li>

            <li class="nav-section">Inventory</li>
            <li><a class="nav-link {{ request()->routeIs('ingredients.*') ? 'active' : '' }}"
                    href="{{ route('ingredients.index') }}">
                    <i class="bi bi-box-seam me-2"></i>Ingredients
                </a></li>
            <li><a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}"
                    href="{{ route('stock-movements.index') }}">
                    <i class="bi bi-journal-text me-2"></i>Stock Logs
                </a></li>

            <li class="nav-section">Orders</li>
            <li><a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                    href="{{ route('orders.index') }}">
                    <i class="bi bi-receipt me-2"></i>Orders
                </a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold text-muted">@yield('title', 'Dashboard')</h6>
            <div class="d-flex align-items-center gap-3">
                @php $lowCount = \App\Models\Ingredient::whereColumn('current_stock', '<=', 'minimum_stock')->count(); @endphp
                @if ($lowCount > 0)
                    <a href="{{ route('ingredients.index') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $lowCount }} Low Stock
                    </a>
                @endif
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus me-1"></i>New Order
                </a>
            </div>
        </div>

        <div class="page-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle me-2"></i>{!! nl2br(e(session('error'))) !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle me-2"></i><strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
