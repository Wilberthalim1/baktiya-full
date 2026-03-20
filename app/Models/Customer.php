<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = ['code','name','company','email','phone','address','city','npwp','status','credit_limit'];

    public function salesOrders() { return $this->hasMany(SalesOrder::class); }
    public function salesInvoices() { return $this->hasMany(SalesInvoice::class); }

    public static function generateCode(): string {
        $last = self::latest()->first();
        $number = $last ? (intval(substr($last->code, 4)) + 1) : 1;
        return 'CUST' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
