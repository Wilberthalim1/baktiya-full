@extends('layouts.app')
@section('title', 'Bayar Invoice Supplier')
@section('page-title', 'Pembayaran ke Supplier')

@section('content')
<div class="mb-3">
    <a href="{{ route('accounting.supplier.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="{{ route('accounting.supplier.store') }}" method="POST">
    @csrf
    <input type="hidden" name="purchase_invoice_id" value="{{ $invoice->id }}">

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header fw-bold bg-primary text-white">
                    <i class="bi bi-cash me-2"></i>Form Pembayaran ke Supplier
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Doc No</label>
                            <input type="text" class="form-control bg-light" value="{{ $docNo }}" readonly>
                            <small class="text-muted">Auto generated</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ref. Invoice No</label>
                            <input type="text" class="form-control bg-light" value="{{ $invoice->inv_number }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vendor Name</label>
                            <input type="text" class="form-control bg-light" value="{{ $invoice->supplier->name }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total Invoice</label>
                            <input type="text" class="form-control bg-light" value="Rp {{ number_format($invoice->total, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Sisa Bayar</label>
                            <input type="text" class="form-control bg-light text-danger fw-bold" value="Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Metode Bayar</label>
                            <input type="text" class="form-control bg-light" value="Transfer Bank" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jumlah Bayar (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" value="{{ $invoice->remaining_amount }}" min="1" max="{{ $invoice->remaining_amount }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Transfer <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Contoh: BCA" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">No. Rekening Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" class="form-control" placeholder="Contoh: 1234567890" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Catatan pembayaran...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white fw-bold">Info Invoice</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">No Invoice</small>
                        <div class="fw-bold text-primary">{{ $invoice->inv_number }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Vendor</small>
                        <div class="fw-bold">{{ $invoice->supplier->name }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Ref. PO No</small>
                        <div>{{ $invoice->purchaseOrder->doc_no }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Tgl Invoice</small>
                        <div>{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Jatuh Tempo</small>
                        <div class="{{ now()->gt($invoice->due_date) ? 'text-danger fw-bold' : '' }}">
                            {{ $invoice->due_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Total Invoice</small>
                        <div class="fw-bold text-primary">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Sisa Bayar</small>
                        <div class="fw-bold text-danger fs-5">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="bi bi-info-circle me-2"></i>
                Pembayaran akan dikirim ke <strong>Management</strong> untuk approval sebelum diproses.
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Submit pembayaran untuk approval management?')">
                    <i class="bi bi-send me-2"></i>Submit untuk Approval
                </button>
                <a href="{{ route('accounting.supplier.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
