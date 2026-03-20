<?php

namespace App\Services;

use App\Models\InventoryTransfer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Support\Facades\Log;

class SalesInvoiceService
{
    public function __construct(
        private InvoiceNumberGenerator $numberGenerator
    ) {}

    /**
     * Membuat SalesInvoice dari InventoryTransfer yang sudah transferred.
     * Mengembalikan null jika invoice sudah ada (duplikat).
     * Dipanggil di dalam DB::transaction yang sudah berjalan.
     */
    public function createFromTransfer(InventoryTransfer $transfer): ?SalesInvoice
    {
        $so = $transfer->salesOrder()->with('items')->first();

        // Cek duplikat
        if (SalesInvoice::where('sales_order_id', $so->id)->exists()) {
            Log::warning("Duplicate invoice skipped for SO #{$so->id}");
            return null;
        }

        // Kalkulasi nilai invoice dari item SO
        $subtotal = $so->items->sum(fn ($item) => $item->quantity * $item->unit_price);
        $tax      = round($subtotal * 0.11, 2);
        $discount = $so->discount ?? 0;
        $total    = $subtotal + $tax - $discount;

        $invoiceDate = now();

        // Buat record sales_invoices
        $invoice = SalesInvoice::create([
            'inv_number'     => $this->numberGenerator->generate(),
            'sales_order_id' => $so->id,
            'customer_id'    => $so->customer_id,
            'created_by'     => $transfer->created_by,
            'invoice_date'   => $invoiceDate,
            'due_date'       => $invoiceDate->copy()->addDays(30),
            'status'         => 'issued',
            'payment_status' => 'unpaid',
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total'          => $total,
            'paid_amount'    => 0,
        ]);

        // Buat record sales_invoice_items dari item SO
        foreach ($so->items as $item) {
            SalesInvoiceItem::create([
                'sales_invoice_id' => $invoice->id,
                'product_id'       => $item->product_id,
                'quantity'         => $item->quantity,
                'unit_price'       => $item->unit_price,
                'discount'         => $item->discount ?? 0,
                'total'            => ($item->quantity * $item->unit_price) - ($item->discount ?? 0),
            ]);
        }

        // Update status sales_orders → completed
        $so->update(['status' => 'completed']);

        return $invoice;
    }
}
