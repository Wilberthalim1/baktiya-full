@extends('layouts.app')
@section('title', 'Buat Purchase Request')
@section('page-title', 'Buat Purchase Request')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('purchasing.pr.store') }}" method="POST" id="prForm">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Doc No</label>
                    <input type="text" class="form-control bg-light" value="{{ $docNo }}" readonly>
                    <small class="text-muted">Auto generated</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Request</label>
                    <input type="date" name="request_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="1" placeholder="Catatan umum PR..."></textarea>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Item Produk <small class="text-muted fw-normal">(maksimal 7 produk)</small></h6>

            <table class="table" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="40%">Nama Produk</th>
                        <th width="15%">Stok Saat Ini</th>
                        <th width="15%">QTY</th>
                        <th width="20%">Remarks</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr id="row-0">
                        <td class="text-center fw-bold row-number">1</td>
                        <td>
                            <select name="items[0][product_id]" class="form-select form-select-sm product-select" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" data-stock="{{ $p->stock_quantity }}" data-min="{{ $p->min_stock }}">
                                    {{ $p->code }} - {{ $p->name }} (Stok: {{ $p->stock_quantity }} {{ $p->unit }})
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <span class="badge bg-secondary stock-badge">-</span>
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control form-control-sm" min="1" required>
                        </td>
                        <td>
                            <select name="items[0][remarks]" class="form-select form-select-sm">
                                <option value="low_stock">Stok Rendah</option>
                                <option value="out_of_stock">Stok Habis</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="addRow">
                <i class="bi bi-plus me-1"></i>Tambah Produk
            </button>

            <div class="d-flex gap-2">
                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                    <i class="bi bi-save me-1"></i>Simpan Draft
                </button>
                <button type="submit" name="action" value="pending" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Submit untuk Approval
                </button>
                <a href="{{ route('purchasing.pr.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = 1;
const MAX_ROWS = 7;

document.getElementById('addRow').addEventListener('click', function() {
    if (document.querySelectorAll('#itemsBody tr').length >= MAX_ROWS) {
        alert('Maksimal 7 produk per PR!');
        return;
    }

    const tbody = document.getElementById('itemsBody');
    const template = document.querySelector('#itemsBody tr').cloneNode(true);
    template.id = 'row-' + rowIndex;

    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        if (el.tagName === 'INPUT') el.value = '';
    });
    template.querySelector('.stock-badge').textContent = '-';
    template.querySelector('.stock-badge').className = 'badge bg-secondary stock-badge';

    tbody.appendChild(template);
    rowIndex++;
    updateNumbers();
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const opt = e.target.selectedOptions[0];
        const row = e.target.closest('tr');
        const badge = row.querySelector('.stock-badge');
        const remarksSelect = row.querySelector('select[name*="remarks"]');

        if (opt && opt.dataset.stock !== undefined) {
            const stock = parseInt(opt.dataset.stock);
            const min = parseInt(opt.dataset.min);
            badge.textContent = stock + ' unit';

            if (stock <= 0) {
                badge.className = 'badge bg-danger stock-badge';
                remarksSelect.value = 'out_of_stock';
            } else if (stock <= min) {
                badge.className = 'badge bg-warning stock-badge';
                remarksSelect.value = 'low_stock';
            } else {
                badge.className = 'badge bg-success stock-badge';
            }
        }
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        if (document.querySelectorAll('#itemsBody tr').length > 1) {
            e.target.closest('tr').remove();
            updateNumbers();
        }
    }
});

function updateNumbers() {
    document.querySelectorAll('.row-number').forEach((el, i) => {
        el.textContent = i + 1;
    });
}
</script>
@endpush
