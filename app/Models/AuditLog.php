<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'action_type',
        'table_affected',
        'record_id',
        'action_timestamp',
        'details',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'action_timestamp' => 'datetime',
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
}