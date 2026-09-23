<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role',
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'user_id', 'user_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'recorded_by', 'user_id');
    }

    public function productions()
    {
        return $this->hasMany(Production::class, 'produced_by', 'user_id');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(
            InventoryTransaction::class,
            'recorded_by',
            'user_id'
        );
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'user_id');
    }

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' .
                ($this->middle_name ? $this->middle_name . ' ' : '') .
                $this->last_name
        );
    }
}
