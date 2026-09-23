<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogger
{
    public function log(
        string $actionType,
        string $tableAffected,
        int $recordId,
        ?string $details = null
    ): void {
        AuditLog::create([
            'user_id' => auth()->user()->user_id,
            'action_type' => $actionType,
            'table_affected' => $tableAffected,
            'record_id' => $recordId,
            'action_timestamp' => now(),
            'details' => $details,
        ]);
    }
}