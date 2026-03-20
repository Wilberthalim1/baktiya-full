<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = ['purchase_invoice_id','product_id','quantity','unit_price','discount','total'];
    public function product() { return $this->belongsTo(Product::class); }
}
