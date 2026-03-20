<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['is_active' => 'boolean'];

    public function salesOrders() { return $this->hasMany(SalesOrder::class, 'sales_id'); }
    public function purchaseRequests() { return $this->hasMany(PurchaseRequest::class, 'requested_by'); }
    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class, 'created_by'); }

    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'admin' => 'danger', 'sales' => 'primary', 'purchasing' => 'warning',
            'invoicing' => 'success', 'warehouse' => 'info', default => 'secondary'
        };
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = [
            'admin' => ['*'],
            'sales' => ['sales.*', 'inventory.view'],
            'purchasing' => ['purchasing.*', 'inventory.*'],
            'invoicing' => ['invoicing.*', 'sales.view', 'purchasing.view'],
            'warehouse' => ['inventory.*', 'goods_receipt.*'],
        ];
        $userPerms = $permissions[$this->role] ?? [];
        if (in_array('*', $userPerms)) return true;
        $module = explode('.', $permission)[0];
        return in_array($permission, $userPerms) || in_array($module . '.*', $userPerms);
    }
}
