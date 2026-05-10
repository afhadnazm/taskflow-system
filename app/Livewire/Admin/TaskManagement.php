<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Task;
use Livewire\Component;

class TaskManagement extends Component
{
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function delete(int $taskId): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $task = Task::with('project')->findOrFail($taskId);

        AuditLog::record(
            'task_deleted',
            'Deleted task ' . $task->title,
            ['task_id' => $task->id, 'title' => $task->title, 'project_id' => $task->project_id],
            []
        );

        $task->delete();
    }

    public function render()
    {
        return view('livewire.admin.task-management', [
            'tasks' => Task::with(['project.user', 'assignedUser', 'claimedByUser'])
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query->where('title', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%')
                            ->orWhere('status', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->get(),
        ])->layout('layouts.app');
    }
}
