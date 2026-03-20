<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    protected $fillable = ['purchase_request_id','product_id','quantity','remarks'];

    public function product() { return $this->belongsTo(Product::class); }
    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }

    public function getRemarksLabelAttribute(): string {
        return match($this->remarks) {
            'low_stock'   => 'Stok Rendah',
            'out_of_stock'=> 'Stok Habis',
            'other'       => 'Lainnya',
            default       => $this->remarks
        };
    }
}
