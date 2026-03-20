<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $fillable = ['doc_no','purchase_request_id','supplier_id','created_by','order_date','req_deliver_date','status','remarks','total_price'];
    protected $casts = ['order_date' => 'date', 'req_deliver_date' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function goodsReceipts() { return $this->hasMany(GoodsReceipt::class); }

    public static function generateDocNo(): string {
        $last = self::withTrashed()->latest()->first();
        $number = $last ? (intval(substr($last->doc_no, 2)) + 1) : 1;
        return 'PO' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function recalculate(): void {
        $total = $this->items->sum('total');
        $this->update(['total_price' => $total]);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft'     => 'secondary',
            'sent'      => 'primary',
            'partial'   => 'warning',
            'received'  => 'success',
            'cancelled' => 'danger',
            default     => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'draft'     => 'Draft',
            'sent'      => 'Dikirim ke Supplier',
            'partial'   => 'Sebagian Diterima',
            'received'  => 'Sudah Diterima',
            'cancelled' => 'Dibatalkan',
            default     => $this->status
        };
    }
}
