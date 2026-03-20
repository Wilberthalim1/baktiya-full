@extends('layouts.app')
@section('title', 'Proses GRPO')
@section('page-title', 'Goods Receipt Purchase Order (GRPO)')

@section('content')
<div class="mb-3">
    <a href="{{ route('invoicing.purchase.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="{{ route('invoicing.purchase.store') }}" method="POST">
    @csrf
    <input type="hidden" name="purchase_order_id" value="{{ $po->id }}">

    <div class="row g-3">
        <div class="col-md-8">
            {{-- Info GRPO --}}
            <div class="card mb-3">
                <div class="card-header fw-bold bg-success text-white">
                    <i class="bi bi-box-seam me-2"></i>Detail GRPO
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Doc No GRPO</label>
                            <input type="text" class="form-control bg-light" value="{{ $docNo }}" readonly>
                            <small class="text-muted">Auto generated</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ref. PO No</label>
                            <input type="text" class="form-control bg-light" value="{{ $po->doc_no }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vendor Name</label>
                            <input type="text" class="form-control bg-light" value="{{ $po->supplier->name }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Terima Barang</label>
                            <input type="date" name="receipt_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Diterima Oleh</label>
                            <input type="text" name="received_by" class="form-control" placeholder="Contoh: Valencia Lim" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Catatan penerimaan...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Item List --}}
            <div class="card">
                <div class="card-header fw-bold">
                    Item dari PO <span class="text-primary">{{ $po->doc_no }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Item Name</th>
                                <th>QTY Di-order</th>
                                <th>QTY Diterima</th>
                                <th>Item Price</th>
                                <th>Total</th>
                                <th>Kondisi</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($po->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $item->product->name }}</strong>
                                <br><small class="text-muted">{{ $item->product->code }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->quantity }} {{ $item->product->unit }}</span>
                            </td>
                            <td>
                                <input type="number" 
                                    name="items[{{ $item->id }}][qty_received]" 
                                    class="form-control form-control-sm qty-input" 
                                    value="{{ $item->quantity }}" 
                                    min="0" 
                                    max="{{ $item->quantity }}" 
                                    required 
                                    style="width:80px"
                                    data-price="{{ $item->unit_price }}">
                            </td>
                            <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td>
                                <strong class="row-total">
                                    Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <select name="items[{{ $item->id }}][condition]" class="form-select form-select-sm" style="width:110px">
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $item->id }}][remarks]" class="form-control form-control-sm" placeholder="Catatan..." style="width:120px">
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total:</td>
                                <td colspan="3"><strong id="grandTotal" class="text-primary">Rp {{ number_format($po->total_price, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white fw-bold">Info PO</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Doc No PO</small>
                        <div class="fw-bold text-primary">{{ $po->doc_no }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Ref. PR No</small>
                        <div class="fw-bold">{{ $po->purchaseRequest->doc_no }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Vendor</small>
                        <div>{{ $po->supplier->name }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Tanggal Order</small>
                        <div>{{ $po->order_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Req. Deliver Date</small>
                        <div class="{{ now()->gt($po->req_deliver_date) ? 'text-danger fw-bold' : '' }}">
                            {{ $po->req_deliver_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Dibuat Oleh</small>
                        <div>{{ $po->creator->name }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Total PO</small>
                        <div class="fw-bold text-primary">Rp {{ number_format($po->total_price, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Konfirmasi penerimaan barang? Stok akan diupdate.')">
                    <i class="bi bi-check-circle me-2"></i>Accept — Terima Barang
                </button>
                <a href="{{ route('invoicing.purchase.index') }}" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function calcTotal() {
    let grand = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const qty = parseFloat(input.value) || 0;
        const price = parseFloat(input.dataset.price) || 0;
        const total = qty * price;
        const row = input.closest('tr');
        const totalEl = row.querySelector('.row-total');
        if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        grand += total;
    });
    document.getElementById('grandTotal').textContent = 'Rp ' + grand.toLocaleString('id-ID');
}
document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('input', calcTotal);
});
calcTotal();
</script>
@endpush
@endsection
