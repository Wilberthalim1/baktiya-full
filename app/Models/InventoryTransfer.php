<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryTransfer extends Model
{
    use SoftDeletes;
    protected $fillable = ['doc_no','sales_order_id','created_by','processed_by','transfer_date','status','giver_name','giver_confirmed_at','receiver_name','receiver_confirmed_at','remarks'];
    protected $casts = ['transfer_date' => 'date', 'giver_confirmed_at' => 'datetime', 'receiver_confirmed_at' => 'datetime'];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
    public function items() { return $this->hasMany(InventoryTransferItem::class); }

    public static function generateDocNo(): string {
        $last = self::withTrashed()->latest()->first();
        $number = $last ? (intval(substr($last->doc_no, 2)) + 1) : 1;
        return 'IT' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'pending'     => 'warning',
            'transferred' => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'pending'     => 'Menunggu Transfer',
            'transferred' => 'Sudah Ditransfer',
            'cancelled'   => 'Dibatalkan',
            default       => $this->status
        };
    }

    public function isStockSufficient(): bool {
        foreach ($this->items as $item) {
            if ($item->product->stock_quantity < $item->qty_request) {
                return false;
            }
        }
        return true;
    }
}
