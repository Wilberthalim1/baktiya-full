@extends('layouts.app')
@section('title', 'Invoice Penjualan')
@section('page-title', 'Invoice Penjualan')

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>No Invoice</th><th>Customer</th><th>Tgl Invoice</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td><strong>{{ $invoice->inv_number }}</strong></td>
                <td>{{ $invoice->customer->name }}</td>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                <td><span class="badge bg-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'partial' ? 'warning' : 'danger') }}">{{ $invoice->payment_status }}</span></td>
                <td><a href="{{ route('invoicing.sales.show', $invoice) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada Invoice</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer">{{ $invoices->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
