@extends('layouts.app')
@section('title', 'Detail Sales Order')
@section('page-title', 'Detail Sales Order')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <div class="d-flex gap-2">
        @if($salesOrder->status === 'draft')
        <form action="{{ route('sales.approve', $salesOrder) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-success">Approve SO</button>
        </form>
        <form action="{{ route('sales.cancel', $salesOrder) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn btn-outline-danger" onclick="return confirm('Batalkan SO ini?')">Batalkan</button>
        </form>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $salesOrder->so_number }}</strong>
                <span class="badge bg-{{ $salesOrder->status_badge }} fs-6">{{ strtoupper($salesOrder->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Customer</small>
                        <div class="fw-bold">{{ $salesOrder->customer->name }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Tanggal Order</small>
                        <div>{{ $salesOrder->order_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Dibutuhkan</small>
                        <div>{{ $salesOrder->required_date->format('d/m/Y') }}</div>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead class="table-light">
                        <tr><th>Produk</th><th>Qty</th><th>Stok Tersedia</th><th>Perlu Beli</th><th>Harga</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                    @foreach($salesOrder->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->qty_available }}</td>
                        <td>
                            @if($item->qty_need_purchase > 0)
                            <span class="badge bg-warning">{{ $item->qty_need_purchase }}</span>
                            @else
                            <span class="text-success">0</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="5" class="text-end">Subtotal:</td><td>Rp {{ number_format($salesOrder->subtotal, 0, ',', '.') }}</td></tr>
                        <tr><td colspan="5" class="text-end">PPN 11%:</td><td>Rp {{ number_format($salesOrder->tax, 0, ',', '.') }}</td></tr>
                        <tr class="fw-bold"><td colspan="5" class="text-end">Total:</td><td>Rp {{ number_format($salesOrder->total, 0, ',', '.') }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-bold">Info</div>
            <div class="card-body">
                <small class="text-muted">Sales</small>
                <div class="mb-2">{{ $salesOrder->sales->name }}</div>
                @if($salesOrder->notes)
                <small class="text-muted">Catatan</small>
                <div>{{ $salesOrder->notes }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
