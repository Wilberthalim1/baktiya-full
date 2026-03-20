@extends('layouts.app')
@section('title', 'Invoice Pembelian')
@section('page-title', 'Invoice Pembelian')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- PO Menunggu GRPO --}}
@if($pending_pos->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning fw-bold">
        <i class="bi bi-clock me-2"></i>Purchase Order Menunggu GRPO ({{ $pending_pos->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Doc No PO</th>
                    <th>Ref. PR No</th>
                    <th>Vendor</th>
                    <th>Tanggal Order</th>
                    <th>Req. Deliver</th>
                    <th>Total Price</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($pending_pos as $po)
            <tr>
                <td><strong class="text-primary">{{ $po->doc_no }}</strong></td>
                <td><span class="badge bg-secondary">{{ $po->purchaseRequest->doc_no }}</span></td>
                <td>{{ $po->supplier->name }}</td>
                <td>{{ $po->order_date->format('d/m/Y') }}</td>
                <td>
                    <span class="{{ now()->gt($po->req_deliver_date) ? 'text-danger fw-bold' : '' }}">
                        {{ $po->req_deliver_date->format('d/m/Y') }}
                    </span>
                </td>
                <td>Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('invoicing.purchase.create', ['po_id' => $po->id]) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-box-seam me-1"></i>Proses GRPO
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
    <i class="bi bi-info-circle me-2"></i>Tidak ada PO yang menunggu GRPO.
</div>
@endif

{{-- Invoice Pembelian Selesai --}}
<div class="card">
    <div class="card-header fw-bold">
        <i class="bi bi-receipt me-2"></i>Riwayat Invoice Pembelian
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>No Invoice</th>
                    <th>Vendor</th>
                    <th>Tgl Invoice</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($completed_invoices as $invoice)
            <tr>
                <td><strong class="text-primary">{{ $invoice->inv_number }}</strong></td>
                <td>{{ $invoice->purchaseOrder->supplier->name }}</td>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                <td>
                    <span class="badge bg-{{ $invoice->payment_status === 'paid' ? 'success' : 'danger' }}">
                        {{ $invoice->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada Invoice Pembelian</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
