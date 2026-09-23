<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'product_id',
        'reserve_type',
        'current_quantity',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'current_quantity' => 'decimal:2',
            'last_updated' => 'datetime',
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

    public function transactions()
    {
        return $this->hasMany(
            InventoryTransaction::class,
            'inventory_id',
            'inventory_id'
        );
    }
}