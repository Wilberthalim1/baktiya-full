@extends('layouts.app')
@section('title', 'Purchase Order')
@section('page-title', 'Purchase Order')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('purchasing.po.create') }}" class="btn btn-primary">
        <i class="bi bi-plus me-1"></i>Buat PO
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Doc No</th>
                    <th>Ref. PR</th>
                    <th>Vendor</th>
                    <th>Tanggal Order</th>
                    <th>Req. Deliver</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pos as $po)
            <tr>
                <td><strong class="text-primary">{{ $po->doc_no }}</strong></td>
                <td><span class="badge bg-secondary">{{ $po->purchaseRequest->doc_no }}</span></td>
                <td>{{ $po->supplier->name }}</td>
                <td>{{ $po->order_date->format('d/m/Y') }}</td>
                <td>{{ $po->req_deliver_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
                <td><span class="badge bg-{{ $po->status_badge }}">{{ $po->status_label }}</span></td>
                <td>
                    <a href="{{ route('purchasing.po.show', $po) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada Purchase Order</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($pos->hasPages())
    <div class="card-footer">{{ $pos->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
