<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','sales','purchasing','invoicing','warehouse','management','accounting') NOT NULL DEFAULT 'sales'");
    }

    public function down(): void {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','sales','purchasing','invoicing','warehouse') NOT NULL DEFAULT 'sales'");
    }
};
