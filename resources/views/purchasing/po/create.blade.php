@extends('layouts.app')
@section('title', 'Buat Purchase Order')
@section('page-title', 'Buat Purchase Order')

@section('content')

<div class="card mb-3">
    <div class="card-header fw-bold bg-light">
        <i class="bi bi-search me-2"></i>Import dari Purchase Request
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('purchasing.po.create') }}" class="d-flex gap-2 align-items-end">
            <div>
                <label class="form-label fw-bold">Masukkan Doc No PR</label>
                <input type="text" name="doc_no" class="form-control" placeholder="Contoh: PR001" value="{{ request('doc_no') }}" style="width:200px">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i>Cari PR
            </button>
            <a href="{{ route('purchasing.pr.index') }}" class="btn btn-outline-secondary">Lihat Daftar PR</a>
        </form>

        @if(isset($error) && $error)
        <div class="alert alert-danger mt-3 mb-0">
            <i class="bi bi-x-circle me-2"></i>{{ $error }}
        </div>
        @endif
    </div>
</div>

@if($pr)
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>PR <strong>{{ $pr->doc_no }}</strong> ditemukan! Silakan lengkapi form di bawah.
</div>

<form action="{{ route('purchasing.po.store') }}" method="POST">
    @csrf
    <input type="hidden" name="purchase_request_id" value="{{ $pr->id }}">

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header fw-bold">Detail Purchase Order</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Doc No PO</label>
                            <input type="text" class="form-control bg-light" value="{{ $docNo }}" readonly>
                            <small class="text-muted">Auto generated</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ref. PR No</label>
                            <input type="text" class="form-control bg-light" value="{{ $pr->doc_no }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vendor Name <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Order</label>
                            <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Req. Deliver Date</label>
                            <input type="date" name="req_deliver_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Catatan...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">
                    Item dari PR <span class="text-primary">{{ $pr->doc_no }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>QTY</th>
                                <th>Price/Item (Rp)</th>
                                <th>Total Price (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pr->items as $i => $item)
                        <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $item->product->name }}</strong>
                                <br><small class="text-muted">{{ $item->product->code }}</small>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm qty-input" value="{{ $item->quantity }}" min="1" required style="width:80px">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm price-input" value="{{ $item->product->cost_price }}" step="0.01" required style="width:140px">
                            </td>
                            <td>
                                <strong class="row-total">Rp {{ number_format($item->quantity * $item->product->cost_price, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Price:</td>
                                <td><strong id="grandTotal">-</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white fw-bold">Info PR</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Doc No PR</small>
                        <div class="fw-bold text-primary fs-5">{{ $pr->doc_no }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Dibuat Oleh</small>
                        <div>{{ $pr->creator->name }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Tanggal Request</small>
                        <div>{{ $pr->request_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Jumlah Item</small>
                        <div>{{ $pr->items->count() }} produk</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle me-2"></i>Buat Purchase Order
                </button>
                <a href="{{ route('purchasing.po.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function calcTotal() {
    let grand = 0;
    document.querySelectorAll('tbody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
        const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
        const total = qty * price;
        const totalEl = row.querySelector('.row-total');
        if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        grand += total;
    });
    document.getElementById('grandTotal').textContent = 'Rp ' + grand.toLocaleString('id-ID');
}
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
        calcTotal();
    }
});
calcTotal();
</script>
@endpush

@endif
@endsection
