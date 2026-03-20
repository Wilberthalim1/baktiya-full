<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPayment extends Model
{
    use SoftDeletes;
    protected $fillable = ['doc_no','purchase_invoice_id','supplier_id','created_by','approved_by','amount','payment_method','bank_name','account_number','payment_date','status','approved_at','remarks'];
    protected $casts = ['payment_date' => 'date', 'approved_at' => 'datetime'];

    public function purchaseInvoice() { return $this->belongsTo(PurchaseInvoice::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    public static function generateDocNo(): string {
        $last = self::withTrashed()->latest()->first();
        $number = $last ? (intval(substr($last->doc_no, 6)) + 1) : 1;
        return 'PAY-S' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft'            => 'secondary',
            'pending_approval' => 'warning',
            'approved'         => 'success',
            'rejected'         => 'danger',
            default            => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'draft'            => 'Draft',
            'pending_approval' => 'Menunggu Approval',
            'approved'         => 'Disetujui / Lunas',
            'rejected'         => 'Ditolak',
            default            => $this->status
        };
    }
}
