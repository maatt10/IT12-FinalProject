<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderComponent extends Model
{
    protected $table = 'order_components';
    protected $primaryKey = 'order_component_id';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}