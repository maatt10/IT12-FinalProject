<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_id',
        'user_id',
        'order_type',
        'receiver_first_name',
        'receiver_middle_name',
        'receiver_last_name',
        'receiver_contact',
        'delivery_address',
        'delivery_timing',
        'fulfillment_type',
        'delivery_fee',
        'payment_proof_reference',
        'order_status',
        'total_amount',
        'order_date',
    ];

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'order_date' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id',
            'customer_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            OrderItem::class,
            'order_id',
            'order_id'
        );
    }

    public function getReceiverFullNameAttribute()
    {
        return trim(
            $this->receiver_first_name . ' ' .
            ($this->receiver_middle_name
                ? $this->receiver_middle_name . ' '
                : '') .
            $this->receiver_last_name
        );
    }
}