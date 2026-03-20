<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id','product_id','quantity','qty_received','unit_price','total'];

    public function product() { return $this->belongsTo(Product::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
}
