@extends('layouts.app')
@section('title', 'Inventory Transfer')
@section('page-title', 'Inventory Transfer')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Pending Transfer --}}
@if($pending->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning fw-bold">
        <i class="bi bi-clock me-2"></i>Menunggu Transfer ({{ $pending->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Doc No IT</th>
                    <th>Ref. SO No</th>
                    <th>Customer</th>
                    <th>Jumlah Item</th>
                    <th>Cek Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($pending as $transfer)
            <tr>
                <td><strong class="text-primary">{{ $transfer->doc_no }}</strong></td>
                <td><span class="badge bg-secondary">{{ $transfer->salesOrder->so_number }}</span></td>
                <td>{{ $transfer->salesOrder->customer->name }}</td>
                <td><span class="badge bg-info">{{ $transfer->items->count() }} produk</span></td>
                <td>
                    @if($transfer->isStockSufficient())
                    <span class="badge bg-success"><i class="bi bi-check me-1"></i>Stok Cukup</span>
                    @else
                    <span class="badge bg-danger"><i class="bi bi-x me-1"></i>Stok Kurang</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('inventory.transfer.show', $transfer) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>Tidak ada transfer yang menunggu diproses.
</div>
@endif

{{-- Riwayat Transfer --}}
<div class="card">
    <div class="card-header fw-bold">
        <i class="bi bi-clock-history me-2"></i>Riwayat Inventory Transfer
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Doc No IT</th>
                    <th>Ref. SO No</th>
                    <th>Customer</th>
                    <th>Tgl Transfer</th>
                    <th>Diproses Oleh</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transferred as $transfer)
            <tr>
                <td><strong class="text-success">{{ $transfer->doc_no }}</strong></td>
                <td><span class="badge bg-secondary">{{ $transfer->salesOrder->so_number }}</span></td>
                <td>{{ $transfer->salesOrder->customer->name }}</td>
                <td>{{ $transfer->transfer_date->format('d/m/Y') }}</td>
                <td>{{ $transfer->processor?->name ?? '-' }}</td>
                <td><span class="badge bg-{{ $transfer->status_badge }}">{{ $transfer->status_label }}</span></td>
                <td>
                    <a href="{{ route('inventory.transfer.show', $transfer) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transfer</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($transferred->hasPages())
    <div class="card-footer">{{ $transferred->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
