<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use SoftDeletes;
    protected $fillable = ['so_number','customer_id','sales_id','order_date','required_date','status','notes','subtotal','discount','tax','total'];
    protected $casts = ['order_date' => 'date', 'required_date' => 'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function sales() { return $this->belongsTo(User::class, 'sales_id'); }
    public function items() { return $this->hasMany(SalesOrderItem::class); }
    public function purchaseRequest() { return $this->hasOne(PurchaseRequest::class); }
    public function invoice() { return $this->hasOne(SalesInvoice::class); }
    public function inventoryTransfer() { return $this->hasOne(InventoryTransfer::class); }

    public static function generateNumber(): string {
        $prefix = 'SO/' . date('Y') . '/' . date('m') . '/';
        $last = self::where('so_number', 'like', $prefix . '%')->latest()->first();
        $seq = $last ? (intval(substr($last->so_number, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function recalculate(): void {
        $subtotal = $this->items->sum('total');
        $tax = $subtotal * 0.11;
        $this->update(['subtotal'=>$subtotal,'tax'=>$tax,'total'=>$subtotal+$tax-$this->discount]);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft'=>'secondary','pending_approval'=>'warning','approved'=>'info',
            'processing'=>'primary','completed'=>'success','cancelled'=>'danger',default=>'secondary'
        };
    }
}
