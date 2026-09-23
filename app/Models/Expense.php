<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'expense_date',
        'amount',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
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