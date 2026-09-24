@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')

<div class="page-header">
    <div>
        <h1>Audit Trail</h1>
        <p>Review important changes made to the system.</p>
    </div>
</div>

<div class="card">

    @if($logs->isEmpty())

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <h3>No audit records found</h3>
        <p>System activity will appear here as users make changes.</p>
    </div>

    @else

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Order Date &amp; Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $log->action_timestamp?->format('M d, Y h:i A') }}
                    </td>

                    <td style="font-weight: 600; color: #212121;">
                        {{ $log->user?->full_name ?? 'Unknown' }}
                    </td>

                    <td>
                        <span class="action-tag action-{{ strtolower($log->action_type) }}">
                            {{ ucfirst($log->action_type) }}
                        </span>
                    </td>

                    <td style="color: #64748B;">
                        {{ $log->table_affected }}
                    </td>

                    <td style="color: #94A3B8; font-size: 12px;">
                        #{{ $log->record_id }}
                    </td>

                    <td style="color: #64748B; max-width: 320px;">
                        {{ $log->details ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif

</div>

<style>
    /* Action tag — plain colored text, non-clickable */
    .action-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .action-create {
        color: #2E5A3B;
    }

    .action-created {
        color: #2E5A3B;
    }

    .action-update {
        color: #B8860B;
    }

    .action-updated {
        color: #B8860B;
    }

    .action-delete {
        color: #DC3545;
    }

    .action-deleted {
        color: #DC3545;
    }

    .action-login {
        color: #E85D75;
    }

    .action-logout {
        color: #64748B;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }

    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .empty-state p {
        font-size: 14px;
    }
</style>

@endsection