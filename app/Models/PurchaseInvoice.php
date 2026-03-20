<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = ['inv_number','purchase_order_id','supplier_id','created_by','invoice_date','due_date','status','payment_status','subtotal','discount','tax','total','paid_amount','notes'];
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function payments() { return $this->hasMany(SupplierPayment::class); }

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
