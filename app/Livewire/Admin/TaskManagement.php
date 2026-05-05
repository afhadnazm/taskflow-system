<?php

namespace App\Livewire\Admin;

use App\Models\Task;
use Livewire\Component;

class TaskManagement extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function delete(int $taskId): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        Task::findOrFail($taskId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.task-management', [
            'tasks' => Task::with(['project.user', 'assignedUser', 'claimedByUser'])->latest()->get(),
        ])->layout('layouts.app');
    }
}
