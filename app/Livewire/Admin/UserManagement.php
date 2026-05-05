<?php

namespace App\Livewire\Admin;

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
        } else {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'manager_id' => $managerId,
            ]);
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

        User::findOrFail($userId)->delete();

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
            'users' => User::with('manager')->latest()->get(),
            'managers' => User::where('role', 'manager')->orderBy('name')->get(),
            'adminsCount' => User::where('role', 'admin')->count(),
            'managersCount' => User::where('role', 'manager')->count(),
            'employeesCount' => User::where('role', 'employee')->count(),
        ])->layout('layouts.app');
    }
}
