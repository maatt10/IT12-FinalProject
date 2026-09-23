<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $primaryKey = 'production_id';

    protected $fillable = [
        'product_id',
        'quantity_produced',
        'produced_by',
        'production_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity_produced' => 'decimal:2',
            'production_date' => 'datetime',
        ];
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_id',
            'product_id'
        );
    }

    public function producedBy()
    {
        return $this->belongsTo(
            User::class,
            'produced_by',
            'user_id'
        );
    }
}