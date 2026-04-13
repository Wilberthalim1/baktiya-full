<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Transfer - {{ $transfer->doc_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header h3 { margin: 5px 0; font-size: 14px; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 5px; }
        .info-table .label { font-weight: bold; width: 150px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th, table.items td { border: 1px solid #000; padding: 5px 8px; }
        table.items th { background: #f0f0f0; text-align: center; }
        table.items td { text-align: center; }
        table.items td:nth-child(2) { text-align: left; }
        .signature-section { display: flex; justify-content: space-between; margin-top: 30px; }
        .signature-box { width: 30%; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 80px; padding-top: 5px; }
        .badge-ok { background: #28a745; color: white; padding: 2px 8px; border-radius: 3px; }
        .badge-warn { background: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:15px;">
    <button onclick="window.print()" style="padding:8px 20px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">
        🖨️ Print Dokumen
    </button>
    <button onclick="window.close()" style="padding:8px 20px; background:#6c757d; color:white; border:none; border-radius:4px; cursor:pointer; margin-left:10px;">
        ✕ Tutup
    </button>
</div>

<div class="header">
    <h2>PT. BAKTIYA UTAMA INDONESIA</h2>
    <h3>INVENTORY TRANSFER DOCUMENT</h3>
    <p style="margin:0">Doc No: <strong>{{ $transfer->doc_no }}</strong></p>
</div>

<table class="info-table">
    <tr>
        <td class="label">Doc No IT</td>
        <td>: {{ $transfer->doc_no }}</td>
        <td class="label">Ref. SO No</td>
        <td>: {{ $transfer->salesOrder->so_number }}</td>
    </tr>
    <tr>
        <td class="label">Customer</td>
        <td>: {{ $transfer->salesOrder->customer->name }}</td>
        <td class="label">Tanggal Transfer</td>
        <td>: {{ $transfer->transfer_date?->format('d/m/Y') ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Dibuat Oleh</td>
        <td>: {{ $transfer->creator->name }}</td>
        <td class="label">Status</td>
        <td>: {{ $transfer->status_label }}</td>
    </tr>
    @if($transfer->remarks)
    <tr>
        <td class="label">Remarks</td>
        <td colspan="3">: {{ $transfer->remarks }}</td>
    </tr>
    @endif
</table>

<table class="items">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">Kode</th>
            <th width="35%">Nama Produk</th>
            <th width="10%">Satuan</th>
            <th width="15%">QTY Request</th>
            <th width="20%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
    @foreach($transfer->items as $i => $item)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $item->product->code }}</td>
        <td style="text-align:left">{{ $item->product->name }}</td>
        <td>{{ $item->product->unit }}</td>
        <td>{{ $item->qty_request }}</td>
        <td>
            @if($item->qty_transfer > 0)
            <span class="badge-ok">Transferred</span>
            @else
            <span class="badge-warn">Pending</span>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<div class="signature-section">
    <div class="signature-box">
        <p><strong>Pemberi Barang</strong></p>
        <p style="font-size:10px; color:#555;">(Petugas Gudang)</p>
        <p>Nama: {{ $transfer->giver_name ?? '____________________' }}</p>
        @if($transfer->giver_confirmed_at)
        <p>Tanggal: {{ $transfer->giver_confirmed_at->format('d/m/Y H:i') }}</p>
        @else
        <p>Tanggal: ____________________</p>
        @endif
        <div class="signature-line">Tanda Tangan</div>
    </div>
    <div class="signature-box">
        <p><strong>Penerima Barang</strong></p>
        <p style="font-size:10px; color:#555;">(Sales)</p>
        <p>Nama: {{ $transfer->receiver_name ?? '____________________' }}</p>
        @if($transfer->receiver_confirmed_at)
        <p>Tanggal: {{ $transfer->receiver_confirmed_at->format('d/m/Y H:i') }}</p>
        @else
        <p>Tanggal: ____________________</p>
        @endif
        <div class="signature-line">Tanda Tangan</div>
    </div>
    <div class="signature-box">
        <p><strong>Penerima Barang</strong></p>
        <p style="font-size:10px; color:#555;">({{ $transfer->salesOrder->customer->name }})</p>
        <p>Nama: ____________________</p>
        <p>Tanggal: ____________________</p>
        <div class="signature-line">Tanda Tangan</div>
        <p style="font-size:9px; color:#333; margin-top:6px; font-style:italic;">
            Dengan menandatangani dokumen ini, pihak penerima menyatakan telah menerima seluruh barang dalam kondisi baik dan lengkap sesuai pesanan, serta <strong>tidak dapat mengajukan klaim atau pengembalian barang</strong> setelah dokumen ini ditandatangani.
        </p>
    </div>
</div>

<div style="margin-top:30px; font-size:10px; text-align:center; color:#666;">
    Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i') }} oleh sistem ERP PT. Baktiya Utama Indonesia
</div>

</body>
</html>
