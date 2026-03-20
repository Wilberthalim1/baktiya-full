<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = ['inv_number','sales_order_id','customer_id','created_by','invoice_date','due_date','status','payment_status','subtotal','discount','tax','total','paid_amount','notes'];
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date'];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(SalesInvoiceItem::class); }
    public function payments() { return $this->hasMany(CustomerPayment::class); }

    public function inventoryTransfer(): HasOneThrough
    {
        return $this->hasOneThrough(
            InventoryTransfer::class,
            SalesOrder::class,
            'id',             // FK di sales_orders (yang di-join dari sales_invoices.sales_order_id)
            'sales_order_id', // FK di inventory_transfers
            'sales_order_id', // local key di sales_invoices
            'id'              // local key di sales_orders
        );
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->payment_status) {
            'unpaid'  => 'danger',
            'partial' => 'warning',
            'paid'    => 'success',
            default   => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string {
        return match($this->payment_status) {
            'unpaid'  => 'Belum Bayar',
            'partial' => 'Sebagian',
            'paid'    => 'Lunas',
            default   => $this->payment_status
        };
    }

    public function getRemainingAmountAttribute(): float {
        return $this->total - $this->paid_amount;
    }
}
