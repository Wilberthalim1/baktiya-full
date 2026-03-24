@extends('layouts.app')
@section('title', 'Pembayaran Supplier')
@section('page-title', 'Pembayaran ke Supplier')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Invoice Menunggu Pembayaran --}}
@if($invoices->count() > 0)
<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white fw-bold">
        <i class="bi bi-exclamation-circle me-2"></i>Invoice Belum Dibayar ({{ $invoices->count() }})
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>No Invoice</th>
                    <th>Vendor</th>
                    <th>Ref. PO</th>
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
                <td>{{ $invoice->supplier->name }}</td>
                <td><span class="badge bg-secondary">{{ $invoice->purchaseOrder->doc_no }}</span></td>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                <td class="text-danger fw-bold">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</td>
                <td><span class="badge bg-{{ $invoice->status_badge }}">{{ $invoice->status_label }}</span></td>
                <td>
                    <a href="{{ route('accounting.supplier.create', ['invoice_id' => $invoice->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-cash me-1"></i>Bayar
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
    <i class="bi bi-check-circle me-2"></i>Semua invoice sudah dibayar!
</div>
@endif

{{-- Riwayat Pembayaran --}}
<div class="card">
    <div class="card-header fw-bold">
        <i class="bi bi-clock-history me-2"></i>Riwayat Pembayaran
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Doc No</th>
                    <th>Vendor</th>
                    <th>Ref. Invoice</th>
                    <th>Jumlah</th>
                    <th>Tgl Bayar</th>
                    <th>Dibuat Oleh</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
            <tr>
                <td><strong class="text-primary">{{ $payment->doc_no }}</strong></td>
                <td>{{ $payment->supplier->name }}</td>
                <td><span class="badge bg-secondary">{{ $payment->purchaseInvoice->inv_number }}</span></td>
                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                <td>{{ $payment->creator->name }}</td>
                <td><span class="badge bg-{{ $payment->status_badge }}">{{ $payment->status_label }}</span></td>
                <td>
                    @if($payment->status === 'pending_approval' && in_array(auth()->user()->role, ['management','admin']))
                    <div class="d-flex gap-1">
                        <form action="{{ route('accounting.supplier.approve', $payment) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success" onclick="return confirm('Setujui pembayaran ini?')">
                                <i class="bi bi-check"></i> Approve
                            </button>
                        </form>
                        <form action="{{ route('accounting.supplier.reject', $payment) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak pembayaran ini?')">
                                <i class="bi bi-x"></i> Tolak
                            </button>
                        </form>
                    </div>
                    @elseif($payment->status === 'pending_approval')
                    <span class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>Menunggu Management</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat pembayaran</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="card-footer">{{ $payments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
