<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Order {{ $po->doc_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #222;
            background: #fff;
            padding: 30px 40px;
        }
        .header { border-bottom: 2px solid #1a3a5c; padding-bottom: 12px; margin-bottom: 18px; }
        .company-name { font-size: 18px; font-weight: bold; color: #1a3a5c; letter-spacing: 0.5px; }
        .company-address { font-size: 10px; color: #555; margin-top: 3px; line-height: 1.5; }
        .doc-title { font-size: 22px; font-weight: bold; color: #1a3a5c; text-align: right; letter-spacing: 1px; }
        .header-left { display: inline-block; width: 60%; vertical-align: top; }
        .header-right { display: inline-block; width: 39%; vertical-align: top; text-align: right; }
        .meta-box {
            background: #f4f7fb;
            border: 1px solid #d0dce8;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 4px 6px; font-size: 11px; vertical-align: top; }
        .meta-table .label { color: #555; width: 130px; }
        .meta-table .colon { width: 10px; color: #555; }
        .meta-table .value { font-weight: bold; color: #222; }
        .supplier-box {
            border: 1px solid #d0dce8;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .supplier-box .box-title { font-weight: bold; color: #1a3a5c; margin-bottom: 6px; font-size: 11px; border-bottom: 1px solid #d0dce8; padding-bottom: 4px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .items-table thead tr { background: #1a3a5c; color: #fff; }
        .items-table thead th { padding: 7px 10px; font-size: 11px; font-weight: bold; text-align: left; }
        .items-table thead th.text-right { text-align: right; }
        .items-table thead th.text-center { text-align: center; }
        .items-table tbody tr { border-bottom: 1px solid #e0e8f0; }
        .items-table tbody tr:nth-child(even) { background: #f9fbfd; }
        .items-table tbody td { padding: 6px 10px; font-size: 11px; vertical-align: middle; }
        .items-table tbody td.text-right { text-align: right; }
        .items-table tbody td.text-center { text-align: center; }
        .summary-wrapper { width: 100%; margin-bottom: 30px; }
        .summary-table { width: 280px; float: right; border-collapse: collapse; }
        .summary-table td { padding: 5px 10px; font-size: 11px; }
        .summary-table .sum-label { color: #555; text-align: left; }
        .summary-table .sum-value { text-align: right; }
        .summary-table .sum-total td { font-size: 13px; font-weight: bold; color: #1a3a5c; border-top: 2px solid #1a3a5c; padding-top: 7px; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .signature-section { width: 100%; margin-top: 10px; border-top: 1px solid #d0dce8; padding-top: 20px; }
        .sig-col { display: inline-block; width: 45%; text-align: center; vertical-align: top; }
        .sig-col-right { float: right; }
        .sig-title { font-size: 11px; font-weight: bold; color: #1a3a5c; margin-bottom: 4px; }
        .sig-area { height: 60px; border-bottom: 1px solid #555; margin: 0 20px 6px 20px; }
        .sig-name { font-size: 11px; color: #222; font-weight: bold; }
        .footer { margin-top: 30px; border-top: 1px solid #d0dce8; padding-top: 8px; font-size: 9px; color: #888; text-align: center; }
        .badge-status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; background: #e8f4fd; color: #1a3a5c; border: 1px solid #b8d4ea; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">PT. Baktiya Utama Indonesia</div>
            <div class="company-address">
                Jl. Raya Industri No. 1, Kawasan Industri Baktiya<br>
                Jakarta Utara, DKI Jakarta 14350<br>
                Telp: (021) 1234-5678 &nbsp;|&nbsp; Email: info@baktiyautama.co.id
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">PURCHASE ORDER</div>
            <div style="margin-top:6px;">
                <span class="badge-status">{{ strtoupper($po->status_label) }}</span>
            </div>
        </div>
    </div>

    {{-- META INFO --}}
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="label">Nomor PO</td>
                <td class="colon">:</td>
                <td class="value">{{ $po->doc_no }}</td>
                <td style="width:40px;"></td>
                <td class="label">Ref. PR No</td>
                <td class="colon">:</td>
                <td class="value">{{ $po->purchaseRequest->doc_no }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Order</td>
                <td class="colon">:</td>
                <td class="value">{{ $po->order_date->format('d F Y') }}</td>
                <td></td>
                <td class="label">Req. Deliver Date</td>
                <td class="colon">:</td>
                <td class="value">{{ $po->req_deliver_date->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Dibuat Oleh</td>
                <td class="colon">:</td>
                <td class="value">{{ $po->creator->name }}</td>
                <td></td>
                @if($po->remarks)
                <td class="label">Keterangan</td>
                <td class="colon">:</td>
                <td class="value">{{ $po->remarks }}</td>
                @endif
            </tr>
        </table>
    </div>

    {{-- SUPPLIER INFO --}}
    <div class="supplier-box">
        <div class="box-title">Kepada Yth. (Supplier)</div>
        <div style="font-weight:bold; font-size:12px; margin-bottom:4px;">{{ $po->supplier->name }}</div>
        @if($po->supplier->address)
        <div style="color:#555; margin-bottom:2px;">{{ $po->supplier->address }}</div>
        @endif
        @if($po->supplier->phone)
        <div style="color:#555;">Telp: {{ $po->supplier->phone }}</div>
        @endif
        @if($po->supplier->email)
        <div style="color:#555;">Email: {{ $po->supplier->email }}</div>
        @endif
    </div>

    <div style="margin-bottom: 18px;"></div>

    {{-- ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px;" class="text-center">No</th>
                <th>Nama Produk</th>
                <th style="width:70px;" class="text-center">Kode</th>
                <th style="width:80px;" class="text-center">QTY</th>
                <th style="width:130px;" class="text-right">Harga Satuan</th>
                <th style="width:130px;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->product->code }}</td>
                <td class="text-center">{{ number_format($item->quantity, 0, ',', '.') }} {{ $item->product->unit }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary-wrapper clearfix">
        <table class="summary-table">
            <tr class="sum-total">
                <td class="sum-label">TOTAL</td>
                <td class="sum-value">Rp {{ number_format($po->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- TERMS --}}
    <div style="margin-top: 10px; padding: 10px 14px; background: #f4f7fb; border: 1px solid #d0dce8; border-radius: 4px; font-size: 10px; color: #555;">
        <strong>Syarat & Ketentuan:</strong><br>
        1. Mohon konfirmasi penerimaan PO ini dalam 1x24 jam.<br>
        2. Pengiriman barang sesuai tanggal yang tertera di atas.<br>
        3. Sertakan dokumen PO ini saat pengiriman barang.
    </div>

    {{-- SIGNATURE --}}
    <div class="signature-section">
        <div class="sig-col">
            <div class="sig-title">Dibuat Oleh</div>
            <div class="sig-area"></div>
            <div class="sig-name">{{ $po->creator->name }}</div>
            <div style="font-size:10px; color:#555;">Purchasing</div>
        </div>
        <div class="sig-col sig-col-right">
            <div class="sig-title">Disetujui Oleh</div>
            <div class="sig-area"></div>
            <div class="sig-name">( ________________________ )</div>
            <div style="font-size:10px; color:#555;">Management</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem &mdash; PT. Baktiya Utama Indonesia &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
