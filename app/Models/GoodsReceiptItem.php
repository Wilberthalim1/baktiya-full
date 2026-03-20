<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    protected $fillable = ['goods_receipt_id','product_id','purchase_order_item_id','qty_ordered','qty_received','unit_price','condition','remarks'];

    public function goodsReceipt() { return $this->belongsTo(GoodsReceipt::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function purchaseOrderItem() { return $this->belongsTo(PurchaseOrderItem::class); }

    public function getConditionLabelAttribute(): string {
        return match($this->condition) {
            'good'    => 'Good',
            'damaged' => 'Damaged',
            default   => $this->condition
        };
    }

    public function getConditionBadgeAttribute(): string {
        return match($this->condition) {
            'good'    => 'success',
            'damaged' => 'danger',
            default   => 'secondary'
        };
    }
}
