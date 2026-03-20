<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $fillable = ['doc_no','sales_invoice_id','customer_id','created_by','amount','payment_method','payment_date','remarks'];
    protected $casts = ['payment_date' => 'date'];

    public function salesInvoice() { return $this->belongsTo(SalesInvoice::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public static function generateDocNo(): string {
        $last = self::latest()->first();
        $number = $last ? (intval(substr($last->doc_no, 6)) + 1) : 1;
        return 'PAY-C' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
