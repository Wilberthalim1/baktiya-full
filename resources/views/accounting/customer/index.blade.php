@extends('layouts.app')
@section('title', 'Pelunasan Customer')
@section('page-title', 'Pelunasan dari Customer')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Invoice Menunggu Pembayaran --}}
@if($invoices->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning fw-bold">
        <i class="bi bi-exclamation-circle me-2"></i>Invoice Belum Lunas ({{ $invoices->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>No Invoice</th>
                    <th>Customer</th>
                    <th>Ref. SO</th>
                    <th>Tgl Invoice</th>
                    <th>Total</th>
                    <th>Sisa Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td><strong class="text-primary">{{ $invoice->inv_number }}</strong></td>
                <td>{{ $invoice->customer->name }}</td>
                <td><span class="badge bg-secondary">{{ $invoice->salesOrder->so_number }}</span></td>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                <td class="text-danger fw-bold">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td>
                <td><span class="badge bg-{{ $invoice->status_badge }}">{{ $invoice->status_label }}</span></td>
                <td>
                    <a href="{{ route('accounting.customer.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-cash me-1"></i>Catat Bayar
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-success mb-4">
    <i class="bi bi-check-circle me-2"></i>Semua invoice sudah lunas!
</div>
@endif

{{-- Riwayat Pembayaran --}}
<div class="card">
    <div class="card-header fw-bold">
        <i class="bi bi-clock-history me-2"></i>Riwayat Pelunasan Customer
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Doc No</th>
                    <th>Customer</th>
                    <th>Ref. Invoice</th>
                    <th>Jumlah</th>
                    <th>Tgl Bayar</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
            <tr>
                <td><strong class="text-success">{{ $payment->doc_no }}</strong></td>
                <td>{{ $payment->customer->name }}</td>
                <td><span class="badge bg-secondary">{{ $payment->salesInvoice->inv_number }}</span></td>
                <td class="text-success fw-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                <td>{{ $payment->creator->name }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pembayaran</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
