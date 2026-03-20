@extends('layouts.app')
@section('title', 'Inventori')
@section('page-title', 'Manajemen Inventori')

@section('content')
<div class="card mb-3">
    <div class="card-body py-2">
        <form class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stock_status" class="form-select">
                <option value="">Semua Stok</option>
                <option value="low" {{ request('stock_status')=='low'?'selected':'' }}>Stok Rendah</option>
                <option value="out" {{ request('stock_status')=='out'?'selected':'' }}>Stok Habis</option>
            </select>
            <button class="btn btn-primary">Cari</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th>Satuan</th><th>Stok</th><th>Min</th><th>Harga Jual</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse($products as $p)
            <tr>
                <td><code>{{ $p->code }}</code></td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name }}</td>
                <td>{{ $p->unit }}</td>
                <td>
                    <span class="fw-bold {{ $p->stock_quantity <= 0 ? 'text-danger' : ($p->stock_quantity <= $p->min_stock ? 'text-warning' : 'text-success') }}">
                        {{ $p->stock_quantity }}
                    </span>
                </td>
                <td>{{ $p->min_stock }}</td>
                <td>Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                <td>
                    @if($p->stock_quantity <= 0)
                    <span class="badge bg-danger">Habis</span>
                    @elseif($p->stock_quantity <= $p->min_stock)
                    <span class="badge bg-warning">Rendah</span>
                    @else
                    <span class="badge bg-success">Normal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada produk ditemukan</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer">{{ $products->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
