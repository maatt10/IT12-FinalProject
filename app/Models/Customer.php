<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'address',
        'is_regular',
    ];

    protected function casts(): array
    {
        return [
            'is_regular' => 'boolean',
        ];
    }

    public function sales()
    {
        return $this->hasMany(
            Sale::class,
            'customer_id',
            'customer_id'
        );
    }

    public function orders()
    {
        return $this->hasMany(
            Order::class,
            'customer_id',
            'customer_id'
        );
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