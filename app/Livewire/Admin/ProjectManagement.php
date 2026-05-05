<?php

namespace App\Livewire\Admin;

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

        Project::findOrFail($projectId)->delete();
    }

    public function render()
    {
        return view('livewire.admin.project-management', [
            'projects' => Project::with(['user', 'tasks'])->latest()->get(),
        ])->layout('layouts.app');
    }
}
