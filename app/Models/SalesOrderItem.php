<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = ['sales_order_id','product_id','quantity','qty_available','qty_need_purchase','unit_price','discount','total'];
    public function product() { return $this->belongsTo(Product::class); }
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
}
