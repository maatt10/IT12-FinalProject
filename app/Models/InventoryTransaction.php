<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions';

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'inventory_id',
        'transaction_type',
        'quantity_change',
        'reference_id',
        'reference_type',
        'notes',
        'transaction_date',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'decimal:2',
            'transaction_date' => 'datetime',
        ];
    }

    public function inventory()
    {
        return $this->belongsTo(
            Inventory::class,
            'inventory_id',
            'inventory_id'
        );
    }

    public function recordedBy()
    {
        return $this->belongsTo(
            User::class,
            'recorded_by',
            'user_id'
        );
    }
}