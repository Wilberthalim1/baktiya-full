<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::table('customers')->where('code', 'CUST0001')->update([
            'name'    => 'PT. Vale Indonesia',
            'company' => 'PT. Vale Indonesia',
            'email'   => 'info@vale.com',
        ]);
        DB::table('customers')->where('code', 'CUST0002')->update([
            'name'    => 'PT. Riung Mitra Lestari',
            'company' => 'PT. Riung Mitra Lestari',
            'email'   => 'cs@riungmitra.com',
        ]);
        // Hapus CUST0003 (PT. Indo Teknologi) jika tidak ada transaksi
        DB::table('customers')->where('code', 'CUST0003')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('sales_orders')->whereColumn('customer_id', 'customers.id');
            })->delete();
    }

    public function down(): void {
        DB::table('customers')->where('code', 'CUST0001')->update(['name' => 'PT. Maju Bersama', 'company' => 'PT. Maju Bersama']);
        DB::table('customers')->where('code', 'CUST0002')->update(['name' => 'CV. Sukses Jaya', 'company' => 'CV. Sukses Jaya']);
    }
};
