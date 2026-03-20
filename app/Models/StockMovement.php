<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['product_id','type','reference_type','reference_id','quantity','stock_before','stock_after','unit_cost','created_by','notes'];
    public function product() { return $this->belongsTo(Product::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
