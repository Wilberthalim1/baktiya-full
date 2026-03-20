<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['payment_number','payment_type','payable_type','payable_id','created_by','payment_date','amount','method','reference','notes'];
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
