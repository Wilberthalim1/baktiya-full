@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-center border-primary">
            <div class="card-body py-3">
                <h3 class="text-primary mb-1">{{ $stats['total_products'] }}</h3>
                <small class="text-muted">Total Produk</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <h3 class="text-warning mb-1">{{ $stats['low_stock'] }}</h3>
                <small class="text-muted">Stok Rendah</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-danger">
            <div class="card-body py-3">
                <h3 class="text-danger mb-1">{{ $stats['out_of_stock'] }}</h3>
                <small class="text-muted">Stok Habis</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-info">
            <div class="card-body py-3">
                <h3 class="text-info mb-1">{{ $stats['total_so'] }}</h3>
                <small class="text-muted">SO Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <h3 class="text-success mb-1">{{ $stats['total_po'] }}</h3>
                <small class="text-muted">PO Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-secondary">
            <div class="card-body py-3">
                <h3 class="text-secondary mb-1">{{ $stats['pending_invoices'] }}</h3>
                <small class="text-muted">Invoice Belum Bayar</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Stok Rendah</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Produk</th><th>Stok</th><th>Min</th></tr></thead>
                    <tbody>
                    @forelse($low_stock_products as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td><span class="badge bg-{{ $p->stock_quantity <= 0 ? 'danger' : 'warning' }}">{{ $p->stock_quantity }}</span></td>
                        <td>{{ $p->min_stock }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Stok semua aman</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-cart text-primary me-2"></i>Sales Order Terbaru</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>No SO</th><th>Customer</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recent_so as $so)
                    <tr>
                        <td><a href="{{ route('sales.show', $so) }}">{{ $so->so_number }}</a></td>
                        <td>{{ $so->customer->name }}</td>
                        <td><span class="badge bg-{{ $so->status_badge }}">{{ $so->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada SO</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
