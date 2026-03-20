<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = ['code','name','category_id','unit','description','cost_price','selling_price','stock_quantity','min_stock','max_stock','location','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function category() { return $this->belongsTo(Category::class); }
    public function stockMovements() { return $this->hasMany(StockMovement::class); }

    public static function generateCode(): string {
        $last = self::latest()->first();
        $number = $last ? (intval(substr($last->code, 3)) + 1) : 1;
        return 'PRD' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getStockStatusAttribute(): string {
        if ($this->stock_quantity <= 0) return 'out_of_stock';
        if ($this->stock_quantity <= $this->min_stock) return 'low_stock';
        return 'normal';
    }

    public function addStock(int $qty, string $refType, int $refId, float $cost, int $userId): void {
        $before = $this->stock_quantity;
        $this->increment('stock_quantity', $qty);
        StockMovement::create(['product_id'=>$this->id,'type'=>'in','reference_type'=>$refType,'reference_id'=>$refId,'quantity'=>$qty,'stock_before'=>$before,'stock_after'=>$before+$qty,'unit_cost'=>$cost,'created_by'=>$userId]);
    }

    public function deductStock(int $qty, string $refType, int $refId, int $userId): void {
        $before = $this->stock_quantity;
        $this->decrement('stock_quantity', $qty);
        StockMovement::create(['product_id'=>$this->id,'type'=>'out','reference_type'=>$refType,'reference_id'=>$refId,'quantity'=>$qty,'stock_before'=>$before,'stock_after'=>$before-$qty,'unit_cost'=>$this->cost_price,'created_by'=>$userId]);
    }
}
