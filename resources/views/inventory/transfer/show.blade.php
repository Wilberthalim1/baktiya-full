@extends('layouts.app')
@section('title', 'Detail IT')
@section('page-title', 'Detail Inventory Transfer')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('inventory.transfer.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if($transfer->status === 'transferred')
        <a href="{{ route('inventory.transfer.print', $transfer) }}" class="btn btn-outline-dark" target="_blank">
            <i class="bi bi-printer me-1"></i>Print Dokumen
        </a>
        @endif
        <span class="badge bg-{{ $transfer->status_badge }} fs-6 px-3 py-2">{{ $transfer->status_label }}</span>
    </div>
</div>

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

<div class="row g-3">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header fw-bold d-flex justify-content-between">
                <span>Detail Inventory Transfer</span>
                <strong class="text-primary">{{ $transfer->doc_no }}</strong>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Ref. SO No</small>
                        <div class="fw-bold">{{ $transfer->salesOrder->so_number }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Customer</small>
                        <div>{{ $transfer->salesOrder->customer->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Dibuat Oleh</small>
                        <div>{{ $transfer->creator->name }}</div>
                    </div>
                    @if($transfer->transfer_date)
                    <div class="col-md-4">
                        <small class="text-muted">Tanggal Transfer</small>
                        <div>{{ $transfer->transfer_date->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    @if($transfer->remarks)
                    <div class="col-md-8">
                        <small class="text-muted">Remarks</small>
                        <div>{{ $transfer->remarks }}</div>
                    </div>
                    @endif
                </div>

                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>QTY Request</th>
                            <th>Stok Tersedia</th>
                            <th>Status Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($transfer->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            <br><small class="text-muted">{{ $item->product->code }}</small>
                        </td>
                        <td><strong>{{ $item->qty_request }}</strong> {{ $item->product->unit }}</td>
                        <td>
                            <span class="badge bg-{{ $item->product->stock_quantity >= $item->qty_request ? 'success' : 'danger' }}">
                                {{ $item->product->stock_quantity }} {{ $item->product->unit }}
                            </span>
                        </td>
                        <td>
                            @if($item->product->stock_quantity >= $item->qty_request)
                            <span class="badge bg-success">Cukup</span>
                            @else
                            <span class="badge bg-danger">Kurang {{ $item->qty_request - $item->product->stock_quantity }} {{ $item->product->unit }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($transfer->status === 'transferred')
        <div class="card border-success">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-check-circle me-2"></i>Informasi Transfer
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block mb-1">Pemberi (Inventori)</small>
                            <strong>{{ $transfer->giver_name }}</strong>
                            <div class="mt-2 border-top pt-2">
                                <small class="text-muted">Dikonfirmasi:</small>
                                <div class="small">{{ $transfer->giver_confirmed_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="mt-3 border rounded" style="height:60px; background:#f8f9fa;">
                                <small class="text-muted d-flex align-items-center justify-content-center h-100">TTD Pemberi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 text-center">
                            <small class="text-muted d-block mb-1">Penerima (Sales)</small>
                            <strong>{{ $transfer->receiver_name }}</strong>
                            <div class="mt-2 border-top pt-2">
                                <small class="text-muted">Dikonfirmasi:</small>
                                <div class="small">{{ $transfer->receiver_confirmed_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="mt-3 border rounded" style="height:60px; background:#f8f9fa;">
                                <small class="text-muted d-flex align-items-center justify-content-center h-100">TTD Penerima</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header fw-bold">Info IT</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Doc No IT</small>
                    <div class="fw-bold text-primary fs-5">{{ $transfer->doc_no }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ref. SO No</small>
                    <div class="fw-bold">{{ $transfer->salesOrder->so_number }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Customer</small>
                    <div>{{ $transfer->salesOrder->customer->name }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Status</small>
                    <div><span class="badge bg-{{ $transfer->status_badge }}">{{ $transfer->status_label }}</span></div>
                </div>
                @if($transfer->status === 'pending')
                <div class="mt-2">
                    @if($transfer->isStockSufficient())
                    <span class="badge bg-success w-100 py-2"><i class="bi bi-check me-1"></i>Stok Mencukupi</span>
                    @else
                    <span class="badge bg-danger w-100 py-2"><i class="bi bi-x me-1"></i>Stok Tidak Mencukupi</span>
                    @endif
                </div>
                @endif
            </div>
        </div>

        @if($transfer->status === 'pending' && $transfer->isStockSufficient())
        <div class="card border-primary">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-arrow-right-circle me-2"></i>Proses Transfer
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.transfer.process', $transfer) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Transfer</label>
                        <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pemberi <small class="text-muted">(Inventori)</small></label>
                        <input type="text" name="giver_name" class="form-control" placeholder="Nama petugas gudang" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Penerima <small class="text-muted">(Sales)</small></label>
                        <input type="text" name="receiver_name" class="form-control" placeholder="Nama penerima dari sales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Catatan transfer..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Konfirmasi transfer barang keluar?')">
                        <i class="bi bi-check-circle me-1"></i>Konfirmasi Transfer
                    </button>
                </form>
            </div>
        </div>
        @elseif($transfer->status === 'pending' && !$transfer->isStockSufficient())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Stok tidak mencukupi! Silakan buat <strong>Purchase Request</strong> terlebih dahulu untuk restok barang.
        </div>
        @endif
    </div>
</div>
@endsection
