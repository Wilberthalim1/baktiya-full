<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransferItem extends Model
{
    protected $fillable = ['inventory_transfer_id','product_id','qty_request','qty_transfer','unit_price'];

    public function inventoryTransfer() { return $this->belongsTo(InventoryTransfer::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function getStockStatusAttribute(): string {
        $stock = $this->product->stock_quantity;
        if ($stock >= $this->qty_request) {
            return 'sufficient';
        } elseif ($stock > 0) {
            return 'partial';
        }
        return 'insufficient';
    }

    public function getStockBadgeAttribute(): string {
        return match($this->stock_status) {
            'sufficient'   => 'success',
            'partial'      => 'warning',
            'insufficient' => 'danger',
        };
    }
}
