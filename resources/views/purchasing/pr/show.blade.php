@extends('layouts.app')
@section('title', 'Detail PR')
@section('page-title', 'Detail Purchase Request')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('purchasing.pr.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if($pr->status === 'pending')
        <form action="{{ route('purchasing.pr.approve', $pr) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Approve PR
            </button>
        </form>
        <form action="{{ route('purchasing.pr.reject', $pr) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-danger" onclick="return confirm('Tolak PR ini?')">
                <i class="bi bi-x-circle me-1"></i>Tolak
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong class="fs-5 text-primary">{{ $pr->doc_no }}</strong>
                    <span class="text-muted ms-2">{{ $pr->request_date->format('d F Y') }}</span>
                </div>
                <span class="badge bg-{{ $pr->status_badge }} fs-6">{{ $pr->status_label }}</span>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Stok Saat Ini</th>
                            <th>QTY</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($pr->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            <br><small class="text-muted">{{ $item->product->code }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->product->stock_quantity <= 0 ? 'danger' : ($item->product->stock_quantity <= $item->product->min_stock ? 'warning' : 'success') }}">
                                {{ $item->product->stock_quantity }} {{ $item->product->unit }}
                            </span>
                        </td>
                        <td><strong>{{ $item->quantity }}</strong> {{ $item->product->unit }}</td>
                        <td>
                            <span class="badge bg-{{ $item->remarks === 'out_of_stock' ? 'danger' : ($item->remarks === 'low_stock' ? 'warning' : 'secondary') }}">
                                {{ $item->remarks_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                @if($pr->remarks)
                <div class="alert alert-light mt-2 mb-0">
                    <small class="text-muted">Catatan PR:</small>
                    <div>{{ $pr->remarks }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header fw-bold">Info PR</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Doc No</small>
                    <div class="fw-bold text-primary fs-5">{{ $pr->doc_no }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Dibuat Oleh</small>
                    <div>{{ $pr->creator->name }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Tanggal Request</small>
                    <div>{{ $pr->request_date->format('d/m/Y') }}</div>
                </div>
                @if($pr->approver)
                <div class="mb-2">
                    <small class="text-muted">Disetujui Oleh</small>
                    <div>{{ $pr->approver->name }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Tanggal Approval</small>
                    <div>{{ $pr->approved_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
                <div>
                    <small class="text-muted">Status</small>
                    <div><span class="badge bg-{{ $pr->status_badge }}">{{ $pr->status_label }}</span></div>
                </div>
            </div>
        </div>

        @if($pr->status === 'approved')
        <div class="card border-success">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-send me-1"></i>Kirim ke Purchasing
            </div>
            <div class="card-body text-center">
                <p class="mb-2 text-muted">Berikan Doc No ini ke Purchasing:</p>
                <div class="display-5 fw-bold text-success border rounded p-3 mb-3 bg-light">
                    {{ $pr->doc_no }}
                </div>
                <p class="text-muted small">Purchasing input Doc No ini di menu Purchase Order untuk membuat PO</p>
            </div>
        </div>
        @endif

        @if($pr->status === 'ordered' && $pr->purchaseOrders->count() > 0)
        <div class="card border-info">
            <div class="card-header bg-info text-white fw-bold">
                <i class="bi bi-bag me-1"></i>Purchase Order
            </div>
            <div class="card-body">
                @foreach($pr->purchaseOrders as $po)
                <a href="{{ route('purchasing.po.show', $po) }}" class="btn btn-outline-info w-100 mb-1">
                    <i class="bi bi-eye me-1"></i>{{ $po->doc_no }} - {{ $po->supplier->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
