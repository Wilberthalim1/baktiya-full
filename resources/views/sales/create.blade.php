@extends('layouts.app')
@section('title', 'Buat Sales Order')
@section('page-title', 'Buat Sales Order Baru')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('sales.store') }}" method="POST" id="soForm">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Order <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Dibutuhkan <span class="text-danger">*</span></label>
                    <input type="date" name="required_date" class="form-control" required>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Item Produk</h6>
            <table class="table" id="itemsTable">
                <thead class="table-light">
                    <tr><th>Produk</th><th>Qty</th><th>Harga Satuan</th><th>Diskon</th><th>Total</th><th></th></tr>
                </thead>
                <tbody id="itemsBody">
                    <tr id="row-0">
                        <td>
                            <select name="items[0][product_id]" class="form-select form-select-sm product-select" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->selling_price }}" data-stock="{{ $p->stock_quantity }}">{{ $p->code }} - {{ $p->name }} (Stok: {{ $p->stock_quantity }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm qty-input" min="1" required></td>
                        <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm price-input" step="0.01" required></td>
                        <td><input type="number" name="items[0][discount]" class="form-control form-control-sm" value="0" step="0.01"></td>
                        <td><input type="text" class="form-control form-control-sm row-total" readonly></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-row">×</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="addRow"><i class="bi bi-plus me-1"></i>Tambah Item</button>

            <div class="row">
                <div class="col-md-4 ms-auto">
                    <table class="table table-sm">
                        <tr><td>Subtotal:</td><td class="text-end" id="subtotalDisplay">Rp 0</td></tr>
                        <tr><td>PPN 11%:</td><td class="text-end" id="taxDisplay">Rp 0</td></tr>
                        <tr class="fw-bold"><td>Total:</td><td class="text-end" id="totalDisplay">Rp 0</td></tr>
                    </table>
                </div>
            </div>
            <input type="hidden" name="discount" value="0">
            <textarea name="notes" class="form-control mb-3" rows="2" placeholder="Catatan (opsional)"></textarea>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Sales Order</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = 1;

function formatRp(n) { return 'Rp ' + parseFloat(n||0).toLocaleString('id-ID'); }

function calcRow(row) {
    const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const disc = parseFloat(row.querySelector('[name*="discount"]')?.value) || 0;
    const total = (qty * price) - disc;
    const totalEl = row.querySelector('.row-total');
    if (totalEl) totalEl.value = formatRp(total);
    return total;
}

function calcAll() {
    let subtotal = 0;
    document.querySelectorAll('#itemsBody tr').forEach(row => { subtotal += calcRow(row); });
    const tax = subtotal * 0.11;
    document.getElementById('subtotalDisplay').textContent = formatRp(subtotal);
    document.getElementById('taxDisplay').textContent = formatRp(tax);
    document.getElementById('totalDisplay').textContent = formatRp(subtotal + tax);
}

document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.getElementById('itemsBody');
    const newRow = document.querySelector('#itemsBody tr').cloneNode(true);
    newRow.id = 'row-' + rowIndex;
    newRow.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/\[0\]/, '[' + rowIndex + ']');
        el.value = el.classList.contains('row-total') ? '' : '';
    });
    tbody.appendChild(newRow);
    rowIndex++;
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const opt = e.target.selectedOptions[0];
        const row = e.target.closest('tr');
        if (opt && opt.dataset.price) {
            row.querySelector('.price-input').value = opt.dataset.price;
            calcAll();
        }
    }
    if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input') || e.target.name?.includes('discount')) {
        calcAll();
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        if (document.querySelectorAll('#itemsBody tr').length > 1) {
            e.target.closest('tr').remove();
            calcAll();
        }
    }
});
</script>
@endpush
