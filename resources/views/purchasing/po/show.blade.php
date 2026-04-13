@extends('layouts.app')
@section('title', 'Detail PO')
@section('page-title', 'Detail Purchase Order')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('purchasing.po.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        @if($po->status === 'pending_approval')
        @if(in_array(auth()->user()->role, ['management','admin']))
        <form action="{{ route('purchasing.po.approve', $po) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-success" onclick="return confirm('Setujui PO ini?')">
                <i class="bi bi-check-circle me-1"></i>Approve PO
            </button>
        </form>
        <form action="{{ route('purchasing.po.reject', $po) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-warning" onclick="return confirm('Kembalikan PO ke Draft?')">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Tolak
            </button>
        </form>
        @else
        <span class="badge bg-warning fs-6 px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Menunggu Approval Management</span>
        @endif
        @elseif($po->status === 'approved')
        @if(in_array(auth()->user()->role, ['purchasing','admin']))
        <form action="{{ route('purchasing.po.send', $po) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-primary" onclick="return confirm('Kirim PO ke Supplier? Pastikan semua item sudah benar.')">
                <i class="bi bi-send me-1"></i>Sent to Supplier
            </button>
        </form>
        @endif
        @elseif($po->status === 'draft')
        @if(in_array(auth()->user()->role, ['management','admin']))
        <form action="{{ route('purchasing.po.cancel', $po) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-danger" onclick="return confirm('Batalkan PO ini?')">
                <i class="bi bi-x-circle me-1"></i>Cancel PO
            </button>
        </form>
        @endif
        @elseif($po->status === 'sent')
        <button class="btn btn-outline-success" disabled>
            <i class="bi bi-check-circle me-1"></i>Sudah Dikirim ke Supplier
        </button>
        @endif
        <a href="{{ route('purchasing.po.pdf', $po) }}" target="_blank" class="btn btn-outline-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF
        </a>
        <span class="badge bg-{{ $po->status_badge }} fs-6 px-3 py-2">{{ $po->status_label }}</span>
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
            <div class="card-header fw-bold d-flex justify-content-between">
                <span>Detail PO</span>
                <strong class="text-primary">{{ $po->doc_no }}</strong>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">Vendor Name</small>
                        <div class="fw-bold">{{ $po->supplier->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Ref. PR No</small>
                        <div><a href="{{ route('purchasing.pr.show', $po->purchaseRequest) }}">{{ $po->purchaseRequest->doc_no }}</a></div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Dibuat Oleh</small>
                        <div>{{ $po->creator->name }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Tanggal Order</small>
                        <div>{{ $po->order_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Req. Deliver Date</small>
                        <div>{{ $po->req_deliver_date->format('d/m/Y') }}</div>
                    </div>
                    @if($po->remarks)
                    <div class="col-md-4">
                        <small class="text-muted">Remarks</small>
                        <div>{{ $po->remarks }}</div>
                    </div>
                    @endif
                </div>
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Item Name</th>
                            <th>QTY Order</th>
                            <th>Price/Item</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($po->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            <br><small class="text-muted">{{ $item->product->code }}</small>
                        </td>
                        <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Price:</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-bold">Info PO</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Doc No PO</small>
                    <div class="fw-bold text-primary fs-5">{{ $po->doc_no }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ref. PR No</small>
                    <div class="fw-bold">{{ $po->purchaseRequest->doc_no }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Vendor</small>
                    <div>{{ $po->supplier->name }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Req. Deliver Date</small>
                    <div>{{ $po->req_deliver_date->format('d/m/Y') }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Total Price</small>
                    <div class="fw-bold text-primary">Rp {{ number_format($po->total_price, 0, ',', '.') }}</div>
                </div>
                <div>
                    <small class="text-muted">Status</small>
                    <div><span class="badge bg-{{ $po->status_badge }}">{{ $po->status_label }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
