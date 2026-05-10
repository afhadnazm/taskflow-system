<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Project;
use Livewire\Component;

class ProjectManagement extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function delete(int $projectId): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $project = Project::with('user')->findOrFail($projectId);

        AuditLog::record(
            'project_deleted',
            'Deleted project ' . $project->name,
            ['project_id' => $project->id, 'name' => $project->name, 'owner_id' => $project->user_id],
            []
        );

        $project->delete();
    }

    public function render()
    {
        return view('livewire.admin.project-management', [
            'projects' => Project::with(['user', 'tasks'])->latest()->get(),
        ])->layout('layouts.app');
    }
}
