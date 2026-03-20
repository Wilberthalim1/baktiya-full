<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    protected $fillable = ['code','name','company','email','phone','address','city','npwp','payment_term','status'];

    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }
    public function purchaseInvoices() { return $this->hasMany(PurchaseInvoice::class); }

    public static function generateCode(): string {
        $last = self::latest()->first();
        $number = $last ? (intval(substr($last->code, 4)) + 1) : 1;
        return 'SUPP' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
