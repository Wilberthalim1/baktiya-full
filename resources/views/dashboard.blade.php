@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- MANAGEMENT APPROVAL SECTION --}}
@if(in_array(auth()->user()->role, ['management','admin']) && $pending_approvals)

{{-- Summary Cards --}}
@php $total_pending = array_sum($pending_approvals); @endphp
@if($total_pending > 0)
<div class="alert alert-warning d-flex align-items-center mb-3">
    <i class="bi bi-bell-fill me-2 fs-5"></i>
    <strong>Ada {{ $total_pending }} item menunggu approval Anda.</strong>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('sales.index') }}" class="text-decoration-none">
            <div class="card border-{{ $pending_approvals['so'] > 0 ? 'warning' : 'success' }} text-center">
                <div class="card-body py-3">
                    <h3 class="mb-1 {{ $pending_approvals['so'] > 0 ? 'text-warning' : 'text-success' }}">{{ $pending_approvals['so'] }}</h3>
                    <small class="text-muted">SO Menunggu Approval</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('purchasing.pr.index') }}" class="text-decoration-none">
            <div class="card border-{{ $pending_approvals['pr'] > 0 ? 'warning' : 'success' }} text-center">
                <div class="card-body py-3">
                    <h3 class="mb-1 {{ $pending_approvals['pr'] > 0 ? 'text-warning' : 'text-success' }}">{{ $pending_approvals['pr'] }}</h3>
                    <small class="text-muted">PR Menunggu Approval</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('purchasing.po.index') }}" class="text-decoration-none">
            <div class="card border-{{ $pending_approvals['po'] > 0 ? 'warning' : 'success' }} text-center">
                <div class="card-body py-3">
                    <h3 class="mb-1 {{ $pending_approvals['po'] > 0 ? 'text-warning' : 'text-success' }}">{{ $pending_approvals['po'] }}</h3>
                    <small class="text-muted">PO Menunggu Approval</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('accounting.supplier.index') }}" class="text-decoration-none">
            <div class="card border-{{ $pending_approvals['payment'] > 0 ? 'warning' : 'success' }} text-center">
                <div class="card-body py-3">
                    <h3 class="mb-1 {{ $pending_approvals['payment'] > 0 ? 'text-warning' : 'text-success' }}">{{ $pending_approvals['payment'] }}</h3>
                    <small class="text-muted">Pembayaran Menunggu Approval</small>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Detail pending items --}}
@if($pending_approvals['so'] > 0)
<div class="card mb-3 border-warning">
    <div class="card-header bg-warning fw-bold"><i class="bi bi-cart me-2"></i>Sales Order Menunggu Approval ({{ $pending_approvals['so'] }})</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>No SO</th><th>Customer</th><th>Sales</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($pending_so as $so)
            <tr>
                <td><strong class="text-primary">{{ $so->so_number }}</strong></td>
                <td>{{ $so->customer->name }}</td>
                <td>{{ $so->sales->name }}</td>
                <td>{{ $so->order_date->format('d/m/Y') }}</td>
                <td><a href="{{ route('sales.show', $so) }}" class="btn btn-sm btn-warning">Review</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($pending_approvals['pr'] > 0)
<div class="card mb-3 border-warning">
    <div class="card-header bg-warning fw-bold"><i class="bi bi-file-text me-2"></i>Purchase Request Menunggu Approval ({{ $pending_approvals['pr'] }})</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Doc No</th><th>Dibuat Oleh</th><th>Jumlah Item</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($pending_pr as $pr)
            <tr>
                <td><strong class="text-primary">{{ $pr->doc_no }}</strong></td>
                <td>{{ $pr->creator->name }}</td>
                <td><span class="badge bg-info">{{ $pr->items->count() }} produk</span></td>
                <td>{{ $pr->request_date->format('d/m/Y') }}</td>
                <td><a href="{{ route('purchasing.pr.show', $pr) }}" class="btn btn-sm btn-warning">Review</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($pending_approvals['po'] > 0)
<div class="card mb-3 border-warning">
    <div class="card-header bg-warning fw-bold"><i class="bi bi-bag me-2"></i>Purchase Order Menunggu Approval ({{ $pending_approvals['po'] }})</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Doc No PO</th><th>Vendor</th><th>Total</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($pending_po as $po)
            <tr>
                <td><strong class="text-primary">{{ $po->doc_no }}</strong></td>
                <td>{{ $po->supplier->name }}</td>
                <td>Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                <td>{{ $po->order_date->format('d/m/Y') }}</td>
                <td><a href="{{ route('purchasing.po.show', $po) }}" class="btn btn-sm btn-warning">Review</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($pending_approvals['payment'] > 0)
<div class="card mb-3 border-warning">
    <div class="card-header bg-warning fw-bold"><i class="bi bi-cash me-2"></i>Pembayaran Supplier Menunggu Approval ({{ $pending_approvals['payment'] }})</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Doc No</th><th>Vendor</th><th>Jumlah</th><th>Dibuat Oleh</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($pending_payment as $payment)
            <tr>
                <td><strong class="text-primary">{{ $payment->doc_no }}</strong></td>
                <td>{{ $payment->supplier->name }}</td>
                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td>{{ $payment->creator->name }}</td>
                <td><a href="{{ route('accounting.supplier.index') }}" class="btn btn-sm btn-warning">Review</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<hr class="my-4">
@endif
{{-- END MANAGEMENT SECTION --}}
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
