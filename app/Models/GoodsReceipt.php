<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceipt extends Model
{
    use SoftDeletes;
    protected $fillable = ['doc_no','purchase_order_id','created_by','received_by','receipt_date','status','remarks'];
    protected $casts = ['receipt_date' => 'date'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(GoodsReceiptItem::class); }

    public static function generateDocNo(): string {
        $last = self::withTrashed()->latest()->first();
        $number = $last ? (intval(substr($last->doc_no, 4)) + 1) : 1;
        return 'GRPO' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'accepted'  => 'success',
            'cancelled' => 'danger',
            default     => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'accepted'  => 'Diterima',
            'cancelled' => 'Dibatalkan',
            default     => $this->status
        };
    }
}
