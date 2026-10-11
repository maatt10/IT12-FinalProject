<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $from = $request->query('from');
        $to = $request->query('to');
        $action = $request->query('action');

        $query = AuditLog::with('user');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('table_affected', 'LIKE', "%{$search}%")
                  ->orWhere('details', 'LIKE', "%{$search}%")
                  ->orWhere('action_type', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('middle_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($from) $query->whereDate('action_timestamp', '>=', $from);
        if ($to) $query->whereDate('action_timestamp', '<=', $to);
        if (in_array($action, ['create', 'update', 'delete'])) $query->where('action_type', $action);

        $logs = $query
            ->orderByDesc('action_timestamp')
            ->paginate(15)
            ->withQueryString();

        return view('audit.index', [
            'logs' => $logs,
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'action' => $action,
        ]);
    }
}