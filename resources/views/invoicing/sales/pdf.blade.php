<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->inv_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #222;
            background: #fff;
            padding: 30px 40px;
        }
        /* ── Header ── */
        .header {
            border-bottom: 2px solid #1a3a5c;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a3a5c;
            letter-spacing: 0.5px;
        }
        .company-address {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
            line-height: 1.5;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #1a3a5c;
            text-align: right;
            letter-spacing: 1px;
        }
        .header-row {
            width: 100%;
        }
        .header-left {
            display: inline-block;
            width: 60%;
            vertical-align: top;
        }
        .header-right {
            display: inline-block;
            width: 39%;
            vertical-align: top;
            text-align: right;
        }
        /* ── Meta Info ── */
        .meta-table {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: top;
        }
        .meta-table .label {
            color: #555;
            width: 130px;
            font-weight: normal;
        }
        .meta-table .colon {
            width: 10px;
            color: #555;
        }
        .meta-table .value {
            font-weight: bold;
            color: #222;
        }
        .meta-box {
            background: #f4f7fb;
            border: 1px solid #d0dce8;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table thead tr {
            background: #1a3a5c;
            color: #fff;
        }
        .items-table thead th {
            padding: 7px 10px;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }
        .items-table thead th.text-right {
            text-align: right;
        }
        .items-table thead th.text-center {
            text-align: center;
        }
        .items-table tbody tr {
            border-bottom: 1px solid #e0e8f0;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f9fbfd;
        }
        .items-table tbody td {
            padding: 6px 10px;
            font-size: 11px;
            vertical-align: middle;
        }
        .items-table tbody td.text-right {
            text-align: right;
        }
        .items-table tbody td.text-center {
            text-align: center;
        }
        /* ── Summary ── */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 10px;
            font-size: 11px;
        }
        .summary-table .sum-label {
            color: #555;
            text-align: left;
        }
        .summary-table .sum-value {
            text-align: right;
            font-weight: normal;
        }
        .summary-table .sum-total td {
            font-size: 13px;
            font-weight: bold;
            color: #1a3a5c;
            border-top: 2px solid #1a3a5c;
            padding-top: 7px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        /* ── Signature ── */
        .signature-section {
            width: 100%;
            margin-top: 10px;
            border-top: 1px solid #d0dce8;
            padding-top: 20px;
        }
        .sig-col {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .sig-col-right {
            float: right;
        }
        .sig-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a3a5c;
            margin-bottom: 4px;
        }
        .sig-area {
            height: 60px;
            border-bottom: 1px solid #555;
            margin: 0 20px 6px 20px;
        }
        .sig-name {
            font-size: 11px;
            color: #222;
            font-weight: bold;
        }
        /* ── Footer ── */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #d0dce8;
            padding-top: 8px;
            font-size: 9px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">PT. Baktiya Utama Indonesia</div>
            <div class="company-address">
                Jl. K.H. Wahid Hasyim No.90A, Sei Sikambing D, Kec. Medan Petisah<br>
                Kota Medan, Sumatera Utara 20119<br>
                Telp: (061) 4571757 &nbsp;|&nbsp; Email: hitler@bhk.co.id
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
        </div>
    </div>

    {{-- ── META INFO ── --}}
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="label">Nomor Invoice</td>
                <td class="colon">:</td>
                <td class="value">{{ $invoice->inv_number }}</td>
                <td style="width:40px;"></td>
                <td class="label">Nomor SO</td>
                <td class="colon">:</td>
                <td class="value">{{ $invoice->salesOrder->so_number ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Customer</td>
                <td class="colon">:</td>
                <td class="value">{{ $invoice->salesOrder->customer->name ?? $invoice->customer->name }}</td>
                <td></td>
                <td class="label">Tanggal Invoice</td>
                <td class="colon">:</td>
                <td class="value">{{ $invoice->invoice_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jatuh Tempo</td>
                <td class="colon">:</td>
                <td class="value">{{ $invoice->due_date->format('d F Y') }}</td>
                <td></td>
                <td class="label">Status</td>
                <td class="colon">:</td>
                <td class="value">{{ strtoupper($invoice->payment_status) }}</td>
            </tr>
        </table>
    </div>

    {{-- ── ITEMS TABLE ── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px;" class="text-center">No</th>
                <th>Nama Produk</th>
                <th style="width:60px;" class="text-center">QTY</th>
                <th style="width:130px;" class="text-right">Harga Satuan</th>
                <th style="width:130px;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── SUMMARY ── --}}
    <div class="summary-wrapper clearfix">
        <table class="summary-table">
            <tr>
                <td class="sum-label">Subtotal</td>
                <td class="sum-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->discount > 0)
            <tr>
                <td class="sum-label">Diskon</td>
                <td class="sum-value">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="sum-label">PPN (11%)</td>
                <td class="sum-value">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</td>
            </tr>
            <tr class="sum-total">
                <td class="sum-label">TOTAL</td>
                <td class="sum-value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- ── SIGNATURE ── --}}
    <div class="signature-section">
        <div class="sig-col">
            <div class="sig-title">Sales / Pemberi Barang</div>
            <div class="sig-area"></div>
            <div class="sig-name">
                {{ $invoice->inventoryTransfer->receiver_name ?? '-' }}
            </div>
        </div>
        <div class="sig-col sig-col-right">
            <div class="sig-title">Penerima Barang</div>
            <div style="font-size:10px; color:#555; margin-bottom:4px;">Perwakilan {{ $invoice->customer->name }}</div>
            <div class="sig-area"></div>
            <div class="sig-name">&nbsp;</div>
        </div>
    </div>

    {{-- ── DISCLAIMER ── --}}
    <div style="margin-top: 20px; padding: 10px 14px; background: #fff8e1; border: 1px solid #f0c040; border-radius: 4px; font-size: 10px; color: #7a5c00; text-align: center;">
        <strong>PERHATIAN:</strong> Dengan ditandatanganinya dokumen ini oleh pihak Penerima Barang, maka barang yang telah diterima
        <strong>tidak dapat dikembalikan (non-returnable)</strong> dan dianggap telah diterima dalam kondisi baik sesuai pesanan.
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem &mdash; PT. Baktiya Utama Indonesia &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
