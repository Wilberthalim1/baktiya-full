<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number', 30)->unique();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('sales_id')->constrained('users');
            $table->date('order_date');
            $table->date('required_date');
            $table->enum('status', ['draft','pending_approval','approved','processing','completed','cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->integer('qty_available')->default(0);
            $table->integer('qty_need_purchase')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 10)->unique();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('request_date');
            $table->enum('status', ['draft','pending','approved','rejected','ordered'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->enum('remarks', ['low_stock','out_of_stock','other'])->default('low_stock');
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 10)->unique();
            $table->foreignId('purchase_request_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->date('order_date');
            $table->date('req_deliver_date');
            $table->enum('status', ['draft','sent','received','cancelled'])->default('draft');
            $table->text('remarks')->nullable();
            $table->decimal('total_price', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->integer('qty_received')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 10)->unique();
            $table->foreignId('purchase_order_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->string('received_by');
            $table->date('receipt_date');
            $table->enum('status', ['accepted','cancelled'])->default('accepted');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('purchase_order_item_id')->constrained();
            $table->integer('qty_ordered');
            $table->integer('qty_received');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->enum('condition', ['good','damaged'])->default('good');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no', 10)->unique();
            $table->foreignId('sales_order_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->date('transfer_date')->nullable();
            $table->enum('status', ['pending','transferred','cancelled'])->default('pending');
            $table->string('giver_name')->nullable();
            $table->timestamp('giver_confirmed_at')->nullable();
            $table->string('receiver_name')->nullable();
            $table->timestamp('receiver_confirmed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('qty_request');
            $table->integer('qty_transfer')->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('type', ['in','out','adjustment'])->default('in');
            $table->integer('quantity');
            $table->integer('stock_before')->default(0);
            $table->integer('stock_after')->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_transfer_items');
        Schema::dropIfExists('inventory_transfers');
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
