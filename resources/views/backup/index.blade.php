@extends('layouts.app')

@section('title', 'Backup & Recovery')

@section('content')

<div class="page-header">
    <div>
        <h1>Backup &amp; Recovery</h1>
        <p>Create and download database backups for the Lara's Flowershop system.</p>
    </div>
</div>

{{-- CREATE BACKUP --}}
<div class="card" style="margin-bottom: 20px; border-top: 4px solid #E85D75;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #FCE4EC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
            </div>
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: #212121;">Database Backup</h2>
                <p style="font-size: 13px; color: #64748B; margin-top: 2px;">
                    Create a complete backup of the current MySQL database.
                </p>
            </div>
        </div>

        <form action="{{ route('backup.create') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Create Database Backup
            </button>
        </form>
    </div>
</div>

{{-- EXISTING BACKUPS --}}
<div class="card" style="margin-bottom: 20px;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>
        Existing Backups
    </h2>

    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
        Previously created database backup files are listed below.
    </p>

    @if($backups->isEmpty())

        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
            </svg>
            <h3>No backups yet</h3>
            <p>Click "Create Database Backup" above to generate your first backup.</p>
        </div>

    @else

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Date &amp; Time</th>
                        <th style="text-align: right;">Size</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($backups as $backup)
                        <tr>
                            <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #212121;">
                                {{ $backup->getFilename() }}
                            </td>

                            <td style="color: #64748B; white-space: nowrap;">
                                {{ date('M d, Y h:i A', $backup->getMTime()) }}
                            </td>

                            <td style="text-align: right; color: #64748B; font-weight: 500;">
                                {{ number_format($backup->getSize() / 1024, 2) }} KB
                            </td>

                            <td style="text-align: right;">
                                <a
                                    href="{{ route('backup.download', ['filename' => $backup->getFilename()]) }}"
                                    class="action-btn view">
                                    Download
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif
</div>

{{-- RECOVERY INSTRUCTIONS --}}
<div class="card">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2E5A3B" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Recovery Instructions
    </h2>

    <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 20px;">
        Database restoration is currently performed manually to prevent accidental replacement of the active database.
    </p>

    <ol class="recovery-steps">

        <li>
            <div class="step-number">1</div>
            <div class="step-content">
                <h3>Stop the application</h3>
                <p>Close the Laravel application or stop the development server before performing database restoration.</p>
            </div>
        </li>

        <li>
            <div class="step-number">2</div>
            <div class="step-content">
                <h3>Open phpMyAdmin</h3>
                <p>Open phpMyAdmin through XAMPP and select the Lara's Flowershop database.</p>
            </div>
        </li>

        <li>
            <div class="step-number">3</div>
            <div class="step-content">
                <h3>Import the backup</h3>
                <p>Use phpMyAdmin's Import function and select the downloaded <strong>.sql</strong> backup file.</p>
            </div>
        </li>

        <li>
            <div class="step-number">4</div>
            <div class="step-content">
                <h3>Verify the database</h3>
                <p>After importing the backup, verify that the database tables and records have been restored correctly before using the system again.</p>
            </div>
        </li>

    </ol>
</div>

<style>
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .action-btn {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .action-btn.view {
        background: #F1F5F9;
        color: #475569;
    }
    .action-btn.view:hover {
        background: #E2E8F0;
        color: #1E293B;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748B;
    }
    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 12px;
        opacity: 0.6;
    }
    .empty-state h3 {
        font-size: 16px;
        color: #212121;
        margin-bottom: 4px;
        font-weight: 700;
    }
    .empty-state p {
        font-size: 13px;
    }

    /* Recovery steps */
    .recovery-steps {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .recovery-steps li {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        padding: 16px;
        background: #FEFCF9;
        border: 1px solid #F5EEE4;
        border-radius: 10px;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #E85D75;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .step-content h3 {
        font-size: 14px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 4px;
    }

    .step-content p {
        font-size: 13px;
        color: #64748B;
        line-height: 1.5;
    }

    .step-content strong {
        color: #E85D75;
    }
</style>

@endsection