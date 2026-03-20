@extends('layouts.app')
@section('title', 'Purchase Request')
@section('page-title', 'Purchase Request')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <form class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Cari Doc No..." value="{{ request('search') }}">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Menunggu Approval</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Disetujui</option>
            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Ditolak</option>
            <option value="ordered" {{ request('status')=='ordered'?'selected':'' }}>Sudah di-PO</option>
        </select>
        <button class="btn btn-outline-secondary">Cari</button>
    </form>
    <a href="{{ route('purchasing.pr.create') }}" class="btn btn-primary">
        <i class="bi bi-plus me-1"></i>Buat PR
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
                    <th>Tanggal</th>
                    <th>Dibuat Oleh</th>
                    <th>Jumlah Item</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($prs as $pr)
            <tr>
                <td><strong class="text-primary">{{ $pr->doc_no }}</strong></td>
                <td>{{ $pr->request_date->format('d/m/Y') }}</td>
                <td>{{ $pr->creator->name }}</td>
                <td><span class="badge bg-info">{{ $pr->items->count() }} produk</span></td>
                <td><span class="badge bg-{{ $pr->status_badge }}">{{ $pr->status_label }}</span></td>
                <td>
                    <a href="{{ route('purchasing.pr.show', $pr) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada Purchase Request</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($prs->hasPages())
    <div class="card-footer">{{ $prs->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
