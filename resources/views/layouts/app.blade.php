<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Baktiya ERP') - PT. Baktiya Utama Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .sidebar { width: 250px; min-height: 100vh; background: #1a237e; position: fixed; top: 0; left: 0; z-index: 100; }
        .sidebar .brand { padding: 20px; color: white; font-weight: bold; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 10px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .sidebar .nav-section { padding: 10px 20px 5px; color: rgba(255,255,255,0.4); font-size: 0.75rem; text-transform: uppercase; }
        .main-content { margin-left: 250px; padding: 20px; }
        .topbar { background: white; padding: 12px 20px; margin: -20px -20px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand"><i class="bi bi-buildings me-2"></i>Baktiya ERP</div>
    <nav class="mt-2">
        <div class="nav-section">Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}"><i class="bi bi-box-seam me-2"></i>Inventori</a>
        <a href="{{ route('inventory.transfer.index') }}" class="nav-link {{ request()->routeIs('inventory.transfer.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right me-2"></i>Inventory Transfer</a>

        @if(in_array(auth()->user()->role, ['admin','sales']))
        <div class="nav-section">Penjualan</div>
        <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"><i class="bi bi-cart me-2"></i>Sales Order</a>
        <a href="{{ route('invoicing.sales.index') }}" class="nav-link {{ request()->routeIs('invoicing.sales.*') ? 'active' : '' }}"><i class="bi bi-receipt me-2"></i>Invoice Penjualan</a>
        @endif

        @if(in_array(auth()->user()->role, ['admin','purchasing']))
        <div class="nav-section">Pembelian</div>
        <a href="{{ route('purchasing.pr.index') }}" class="nav-link {{ request()->routeIs('purchasing.pr.*') ? 'active' : '' }}"><i class="bi bi-file-text me-2"></i>Purchase Request</a>
        <a href="{{ route('purchasing.po.index') }}" class="nav-link {{ request()->routeIs('purchasing.po.*') ? 'active' : '' }}"><i class="bi bi-bag me-2"></i>Purchase Order</a>
        <a href="{{ route('invoicing.purchase.index') }}" class="nav-link {{ request()->routeIs('invoicing.purchase.*') ? 'active' : '' }}"><i class="bi bi-receipt-cutoff me-2"></i>Invoice Pembelian</a>
        <div class="nav-section">ACCOUNTING</div>
        <a href="{{ route('accounting.supplier.index') }}" class="nav-link {{ request()->routeIs('accounting.supplier.*') ? 'active' : '' }}"><i class="bi bi-cash me-2"></i>Bayar Supplier</a>
        <a href="{{ route('accounting.customer.index') }}" class="nav-link {{ request()->routeIs('accounting.customer.*') ? 'active' : '' }}"><i class="bi bi-cash-coin me-2"></i>Pelunasan Customer</a>
        @endif
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                <span class="badge bg-{{ auth()->user()->role_badge }} ms-1">{{ auth()->user()->role }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
