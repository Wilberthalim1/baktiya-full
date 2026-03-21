<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::table('suppliers')->where('code', 'SUPP0001')->update([
            'name'    => 'PT. Sedaya Mitra Sejahtera',
            'company' => 'PT. Sedaya Mitra Sejahtera',
        ]);
        DB::table('suppliers')->where('code', 'SUPP0002')->update([
            'name'    => '3M Indonesia',
            'company' => '3M Indonesia',
        ]);
    }

    public function down(): void {
        DB::table('suppliers')->where('code', 'SUPP0001')->update([
            'name'    => 'PT. Sumber Elektronik',
            'company' => 'PT. Sumber Elektronik',
        ]);
        DB::table('suppliers')->where('code', 'SUPP0002')->update([
            'name'    => 'CV. Teknik Jaya',
            'company' => 'CV. Teknik Jaya',
        ]);
    }
};
