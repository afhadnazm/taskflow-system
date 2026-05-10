<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserManagement extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'employee';
    public string $search = '';
    public ?int $manager_id = null;
    public ?int $editingUserId = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->editingUserId)],
            'password' => [$this->editingUserId ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'manager', 'employee'])],
            'manager_id' => ['nullable', 'exists:users,id'],
        ]);

        $managerId = $validated['role'] === 'employee' ? ($validated['manager_id'] ?: null) : null;

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $oldValues = $user->only(['name', 'email', 'role', 'manager_id']);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'manager_id' => $managerId,
            ];

            if ($validated['password'] !== '') {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            $newValues = $user->fresh()->only(['name', 'email', 'role', 'manager_id']);

            if ($oldValues['role'] !== $newValues['role']) {
                AuditLog::record(
                    'user_role_changed',
                    'Changed role for ' . $user->name . ' from ' . $oldValues['role'] . ' to ' . $newValues['role'],
                    ['role' => $oldValues['role']],
                    ['role' => $newValues['role']],
                    $user->id
                );
            }

            if ((int) $oldValues['manager_id'] !== (int) $newValues['manager_id']) {
                AuditLog::record(
                    'user_manager_changed',
                    'Changed manager assignment for ' . $user->name,
                    ['manager_id' => $oldValues['manager_id']],
                    ['manager_id' => $newValues['manager_id']],
                    $user->id
                );
            }
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'manager_id' => $managerId,
            ]);

            AuditLog::record(
                'user_created',
                'Created user ' . $user->name . ' with role ' . $user->role,
                [],
                $user->only(['name', 'email', 'role', 'manager_id']),
                $user->id
            );
        }

        $this->resetForm();
    }

    public function edit(int $userId): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->manager_id = $user->manager_id;
    }

    public function delete(int $userId): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
        abort_if(auth()->id() === $userId, 403);

        $user = User::findOrFail($userId);
        $oldValues = $user->only(['name', 'email', 'role', 'manager_id']);

        AuditLog::record(
            'user_deleted',
            'Deleted user ' . $user->name,
            $oldValues,
            [],
            $user->id
        );

        $user->delete();

        if ($this->editingUserId === $userId) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'manager_id', 'editingUserId']);
        $this->role = 'employee';
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => User::with('manager')
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('role', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->get(),
            'managers' => User::where('role', 'manager')->orderBy('name')->get(),
            'adminsCount' => User::where('role', 'admin')->count(),
            'managersCount' => User::where('role', 'manager')->count(),
            'employeesCount' => User::where('role', 'employee')->count(),
        ])->layout('layouts.app');
    }
}
