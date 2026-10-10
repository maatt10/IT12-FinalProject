@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="page-header">
    <div>
        <h1>User Management</h1>
        <p>Manage owner and staff accounts for the shop.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

{{-- TABS --}}
<div class="user-tabs">
    <a href="{{ route('users.index') }}"
       class="user-tab {{ !$showArchived ? 'active' : '' }}">
        Active
    </a>
    <a href="{{ route('users.index', ['archived' => '1']) }}"
       class="user-tab {{ $showArchived ? 'active' : '' }}">
        Archived
    </a>
</div>

{{-- ACTION BAR --}}
@if(!$showArchived)
    <div class="item-actions">
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Add User</a>
    </div>
@endif

{{-- TABLE --}}
<div class="card">
    @if($users->isEmpty())
        <div class="empty-state">
            @if($showArchived)
                <h3>No archived users</h3>
                <p>Archived accounts will appear here.</p>
            @else
                <h3>No users yet</h3>
                <p>Click "+ Add User" to create the first one.</p>
            @endif
        </div>
    @else
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="num">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td style="font-weight: 600;">
                                {{ $user->full_name }}
                                @if($user->user_id === auth()->id())
                                    <span class="you-badge">You</span>
                                @endif
                            </td>
                            <td style="color: #64748B; font-family: 'SF Mono', Consolas, monospace; font-size: 13px;">
                                {{ $user->username }}
                            </td>
                            <td>
                                @if($user->role === 'owner')
                                    <span class="role-badge owner">Owner</span>
                                @else
                                    <span class="role-badge staff">Staff</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="status-dot active"></span> Active
                                @else
                                    <span class="status-dot archived"></span> Archived
                                @endif
                            </td>
                            <td class="num">
                                <div class="action-buttons">
                                    @if($user->is_active)
                                        <a href="{{ route('users.edit', $user) }}" class="action-btn edit">Edit</a>

                                        @if($user->user_id !== auth()->id())
                                            <form action="{{ route('users.archive', $user) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Archive this user? They will no longer be able to log in.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn archive">Archive</button>
                                            </form>
                                        @endif
                                    @else
                                        <form action="{{ route('users.restore', $user) }}"
                                              method="POST"
                                              style="display: inline;"
                                              onsubmit="return confirm('Restore this user? They will be able to log in again.');">
                                            @csrf
                                            <button type="submit" class="action-btn restore">Restore</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
    .user-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F0E6DD;
    }
    .user-tab {
        display: inline-flex;
        align-items: center;
        padding: 10px 2px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px;
        transition: all 0.15s ease;
    }
    .user-tab:hover { color: #6B5B95; }
    .user-tab.active {
        color: #6B5B95;
        border-bottom-color: #6B5B95;
    }

    .item-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .item-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 42px;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
    }

    .you-badge {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 4px;
        background: #EFEBF7;
        color: #6B5B95;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .role-badge.owner {
        background: #EFEBF7;
        color: #6B5B95;
    }
    .role-badge.staff {
        background: #F1F5F9;
        color: #475569;
    }

    .status-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
        vertical-align: middle;
    }
    .status-dot.active { background: #2E5A3B; }
    .status-dot.archived { background: #94A3B8; }

    .action-buttons { display: flex; gap: 6px; justify-content: flex-end; }
    .action-btn {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-family: inherit;
    }
    .action-btn.edit { background: #EFEBF7; color: #6B5B95; }
    .action-btn.edit:hover { background: #D5C9E8; color: #594B7D; }
    .action-btn.archive { background: #FFF4E5; color: #A16207; }
    .action-btn.archive:hover { background: #FDE9C7; color: #854D0E; }
    .action-btn.restore { background: #E8F5E9; color: #2E5A3B; }
    .action-btn.restore:hover { background: #D4EBD6; color: #1E3D28; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }
    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .empty-state p { font-size: 14px; }

    table td.num, table th.num {
        text-align: right;
        white-space: nowrap;
    }
</style>

@endsection