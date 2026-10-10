<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'reference_code',
        'customer_id',
        'customer_name',
        'user_id',
        'order_type',
        'channel',
        'receiver_first_name',
        'receiver_middle_name',
        'receiver_last_name',
        'receiver_contact',
        'delivery_address',
        'delivery_datetime',
        'fulfillment_type',
        'delivery_fee',
        'subtotal',
        'discount_type',
        'discount_name',
        'discount_id_number',
        'discount_amount',
        'payment_method',
        'payment_reference',
        'payment_proof_reference',
        'amount_paid',
        'change_amount',
        'order_status',
        'cancellation_note',
        'total_amount',
        'order_date',
    ];

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'delivery_datetime' => 'datetime',
            'order_date' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function components()
    {
        return $this->hasMany(OrderComponent::class, 'order_id', 'order_id');
    }

    public function getReceiverFullNameAttribute()
    {
        return trim(
            $this->receiver_first_name . ' ' .
            ($this->receiver_middle_name ? $this->receiver_middle_name . ' ' : '') .
            $this->receiver_last_name
        );
    }
}