<?php

namespace App\Services;

use App\Models\SalesInvoice;

class InvoiceNumberGenerator
{
    /**
     * Menghasilkan nomor invoice format "Inv-SO{NNN}" yang unik.
     * Menggunakan withTrashed() agar soft-deleted invoice tetap dihitung.
     */
    public function generate(): string
    {
        $last = SalesInvoice::withTrashed()->latest('id')->first();
        $seq  = $last ? ($last->id + 1) : 1;
        return 'Inv-SO' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
