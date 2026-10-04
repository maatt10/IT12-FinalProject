<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'reference_code',
        'customer_id',
        'user_id',
        'sale_date',
        'payment_method',
        'gcash_reference',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_name',
        'discount_id_number',
        'total_amount',
        'receipt_issued',
    ];


    protected function casts(): array
    {
        return [
            'sale_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'receipt_issued' => 'boolean',
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
            SaleItem::class,
            'sale_id',
            'sale_id'
        );
    }
}
