<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class Projects extends Component
{
    use AuthorizesRequests;
    public $name;
    public $description;
    public $successMessage;
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->projects()->create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->reset(['name', 'description']);
        $this->successMessage = "Project created successfully!";
    }

    public function delete($id)
    {
        $project = Project::findOrFail($id);

        $this->authorize('delete', $project);

        $project->delete();
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->role === 'manager') {
            $projects = $user->projects()->latest()->get();
        } else {
            $projects = \App\Models\Project::where('user_id', $user->manager_id)->latest()->get();
        }

        return view('livewire.projects', [
            'projects' => $projects
        ]);
    }
}