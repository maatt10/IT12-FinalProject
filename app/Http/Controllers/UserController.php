<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->query('archived') === '1';

        $users = User::where('is_active', !$showArchived)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('users.index', compact('users', 'showArchived'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:owner,staff'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create($validated);

        app(AuditLogger::class)->log(
            'create',
            'users',
            $user->user_id,
            'User created: ' . $user->full_name . ' (' . $user->role . ', @' . $user->username . ')'
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:owner,staff'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->user_id, 'user_id'),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // Guard: cannot demote the last active owner
        if ($user->role === 'owner' && $validated['role'] !== 'owner') {
            $otherOwners = User::where('role', 'owner')
                ->where('is_active', true)
                ->where('user_id', '!=', $user->user_id)
                ->count();

            if ($otherOwners === 0) {
                return back()
                    ->withErrors(['role' => 'Cannot change role — this is the last active owner account.'])
                    ->withInput();
            }
        }

        // Guard: cannot deactivate yourself by changing role to nothing
        if ($user->user_id === auth()->id() && $validated['role'] !== 'owner') {
            $otherOwners = User::where('role', 'owner')
                ->where('is_active', true)
                ->where('user_id', '!=', $user->user_id)
                ->count();

            if ($otherOwners === 0) {
                return back()
                    ->withErrors(['role' => 'You are the only owner — you cannot change your own role.'])
                    ->withInput();
            }
        }

        // Remove blank password so it doesn't overwrite the existing one
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        app(AuditLogger::class)->log(
            'update',
            'users',
            $user->user_id,
            'User updated: ' . $user->full_name . ' (' . $user->role . ', @' . $user->username . ')'
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function archive(User $user)
    {
        // Guard: cannot archive yourself
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'You cannot archive your own account.');
        }

        // Guard: cannot archive the last active owner
        if ($user->role === 'owner') {
            $otherOwners = User::where('role', 'owner')
                ->where('is_active', true)
                ->where('user_id', '!=', $user->user_id)
                ->count();

            if ($otherOwners === 0) {
                return back()->with('error', 'Cannot archive the last active owner account.');
            }
        }

        $user->update(['is_active' => false]);

        app(AuditLogger::class)->log(
            'update',
            'users',
            $user->user_id,
            'User archived: ' . $user->full_name . ' (@' . $user->username . ')'
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'User archived successfully.');
    }

    public function restore(User $user)
    {
        $user->update(['is_active' => true]);

        app(AuditLogger::class)->log(
            'update',
            'users',
            $user->user_id,
            'User restored: ' . $user->full_name . ' (@' . $user->username . ')'
        );

        return redirect()
            ->route('users.index', ['archived' => '1'])
            ->with('success', 'User restored successfully.');
    }
}