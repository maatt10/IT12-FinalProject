<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'supplier_name',
        'user_id',
        'purchase_date',
        'total_amount',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'purchase_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
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
            PurchaseItem::class,
            'purchase_id',
            'purchase_id'
        );
    }
}