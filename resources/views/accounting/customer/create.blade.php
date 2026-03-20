@extends('layouts.app')
@section('title', 'Catat Pelunasan Customer')
@section('page-title', 'Catat Pelunasan dari Customer')

@section('content')
<div class="mb-3">
    <a href="{{ route('accounting.customer.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="{{ route('accounting.customer.store') }}" method="POST">
    @csrf
    <input type="hidden" name="sales_invoice_id" value="{{ $invoice->id }}">

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header fw-bold bg-success text-white">
                    <i class="bi bi-cash me-2"></i>Form Pelunasan dari Customer
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
                            <label class="form-label fw-bold">Customer</label>
                            <input type="text" class="form-control bg-light" value="{{ $invoice->customer->name }}" readonly>
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
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Bayar (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" value="{{ $invoice->remaining_amount }}" min="1" max="{{ $invoice->remaining_amount }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Remarks</label>
                            <input type="text" name="remarks" class="form-control" placeholder="Catatan pembayaran...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white fw-bold">Info Invoice</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">No Invoice</small>
                        <div class="fw-bold text-primary">{{ $invoice->inv_number }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Customer</small>
                        <div class="fw-bold">{{ $invoice->customer->name }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Ref. SO No</small>
                        <div>{{ $invoice->salesOrder->so_number }}</div>
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

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Catat pembayaran dari customer ini?')">
                    <i class="bi bi-check-circle me-2"></i>Catat Pembayaran
                </button>
                <a href="{{ route('accounting.customer.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
