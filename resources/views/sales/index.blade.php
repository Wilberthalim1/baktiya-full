@extends('layouts.app')
@section('title', 'Sales Order')
@section('page-title', 'Sales Order')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <form class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Cari SO..." value="{{ request('search') }}">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
        </select>
        <button class="btn btn-outline-secondary">Cari</button>
    </form>
    <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="bi bi-plus me-1"></i>Buat SO</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>No SO</th><th>Customer</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($salesOrders as $so)
            <tr>
                <td><strong>{{ $so->so_number }}</strong></td>
                <td>{{ $so->customer->name }}</td>
                <td>{{ $so->order_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($so->total, 0, ',', '.') }}</td>
                <td><span class="badge bg-{{ $so->status_badge }}">{{ $so->status }}</span></td>
                <td><a href="{{ route('sales.show', $so) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada Sales Order</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($salesOrders->hasPages())
    <div class="card-footer">{{ $salesOrders->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
