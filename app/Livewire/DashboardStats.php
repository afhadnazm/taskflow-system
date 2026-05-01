<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        return view('livewire.dashboard-stats', [
            'projectsCount' => Project::where('user_id', auth()->id())->count(),

            'totalTasks' => Task::whereHas('project', function ($query) {
                $query->where('user_id', auth()->id());
            })->count(),

            'todoTasks' => Task::where('status', 'todo')->whereHas('project', function ($query) {
                $query->where('user_id', auth()->id());
            })->count(),

            'progressTasks' => Task::where('status', 'in_progress')->whereHas('project', function ($query) {
                $query->where('user_id', auth()->id());
            })->count(),

            'doneTasks' => Task::where('status', 'done')->whereHas('project', function ($query) {
                $query->where('user_id', auth()->id());
            })->count(),

            'myTasks' => Task::where('assigned_to', auth()->id())->count(),
        ]);
    }
}