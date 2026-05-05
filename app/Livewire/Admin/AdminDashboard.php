<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'usersCount' => User::count(),
            'adminsCount' => User::where('role', 'admin')->count(),
            'managersCount' => User::where('role', 'manager')->count(),
            'employeesCount' => User::where('role', 'employee')->count(),
            'projectsCount' => Project::count(),
            'tasksCount' => Task::count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentTasks' => Task::with(['project', 'assignedUser'])->latest()->take(5)->get(),
        ])->layout('layouts.app');
    }
}
