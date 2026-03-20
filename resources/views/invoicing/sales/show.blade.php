@extends('layouts.app')
@section('title', 'Detail Invoice')
@section('page-title', 'Detail Invoice Penjualan')

@section('content')
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('invoicing.sales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <a href="{{ route('invoicing.sales.pdf', $invoice) }}" class="btn btn-outline-danger" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Cetak PDF</a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $invoice->inv_number }}</strong>
                <span class="badge bg-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'partial' ? 'warning' : 'danger') }} fs-6">
                    {{ strtoupper($invoice->payment_status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Customer</small>
                        <div class="fw-bold">{{ $invoice->customer->name }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Tgl Invoice</small>
                        <div>{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Jatuh Tempo</small>
                        <div class="{{ $invoice->due_date < now() && $invoice->payment_status !== 'paid' ? 'text-danger fw-bold' : '' }}">{{ $invoice->due_date->format('d/m/Y') }}</div>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead class="table-light"><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Total</th></tr></thead>
                    <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end">Total:</td><td class="fw-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="3" class="text-end text-success">Sudah Dibayar:</td><td class="text-success">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="3" class="text-end text-danger fw-bold">Sisa:</td><td class="text-danger fw-bold">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($invoice->inventoryTransfer)
        <div class="card mt-3">
            <div class="card-header fw-bold">Informasi Pengiriman & TTD</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <small class="text-muted d-block mb-4">Sales / Pemberi Barang</small>
                        <div class="border-top pt-2 fw-bold">
                            {{ $invoice->inventoryTransfer->creator->name ?? $invoice->inventoryTransfer->giver_name ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <small class="text-muted d-block mb-4">Penerima Barang</small>
                        <div class="border-top pt-2 fw-bold">
                            {{ $invoice->inventoryTransfer->receiver_name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-bold">Info Pembayaran</div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status</small>
                    <div>
                        <span class="badge fs-6 bg-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'partial' ? 'warning' : 'danger') }}">
                            {{ $invoice->payment_status === 'paid' ? 'Lunas' : ($invoice->payment_status === 'partial' ? 'Sebagian' : 'Belum Dibayar') }}
                        </span>
                    </div>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <small class="text-muted">Total Invoice</small>
                    <span class="fw-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <small class="text-muted">Sudah Dibayar</small>
                    <span class="text-success">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between border-top pt-2">
                    <small class="text-muted fw-bold">Sisa Tagihan</small>
                    <span class="fw-bold text-danger">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</span>
                </div>
                @if($invoice->payment_status !== 'paid')
                <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:12px;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Belum lunas. Silakan follow up ke customer.
                </div>
                @endif
                <a href="{{ route('accounting.customer.index') }}" class="btn btn-outline-primary w-100 btn-sm">
                    <i class="bi bi-bank me-1"></i>Lihat Riwayat Pembayaran
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
