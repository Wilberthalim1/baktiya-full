<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use SoftDeletes;
    protected $fillable = ['doc_no','created_by','approved_by','request_date','status','remarks','approved_at'];
    protected $casts = ['request_date' => 'date', 'approved_at' => 'datetime'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function items() { return $this->hasMany(PurchaseRequestItem::class); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }

    public static function generateDocNo(): string {
        $last = self::withTrashed()->latest()->first();
        $number = $last ? (intval(substr($last->doc_no, 2)) + 1) : 1;
        return 'PR' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft'    => 'secondary',
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'ordered'  => 'info',
            default    => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'draft'    => 'Draft',
            'pending'  => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'ordered'  => 'Sudah di-PO',
            default    => $this->status
        };
    }
}
