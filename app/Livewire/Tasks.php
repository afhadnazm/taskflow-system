<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Task;
use Livewire\Component;
use App\Models\TaskComment;
use App\Models\ActivityLog;
use App\Notifications\TaskAssignedNotification;
class Tasks extends Component
{
    public Project $project;

    public $title = '';
    public $description = '';
    public $status = 'todo';
    public $priority = 'medium';
    public $due_date = '';
    public $assigned_to;
    public $editingTaskId = null;
    public $buttonText = 'Add Task';
    public $showMyTasks = false;
    public $commentText = [];
    public function mount(Project $project)
    {
        $this->project = $project;
    }
    public function save()
    {
        if (auth()->user()->role !== 'manager') {
            abort(403);
        }
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($this->editingTaskId) {
            $task = Task::where('project_id', $this->project->id)
                ->findOrFail($this->editingTaskId);

            $previousAssignee = $task->assigned_to;

            $task->update([
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'due_date' => $this->due_date ?: null,
                'assigned_to' => $this->assigned_to ?: null,
            ]);

            if ($this->assigned_to && (int) $previousAssignee !== (int) $this->assigned_to) {
                $task->assignedUser?->notify(new TaskAssignedNotification($task));
            }

            $this->editingTaskId = null;
            $this->buttonText = 'Add Task';
        } else {
            $task = Task::create([
                'project_id' => $this->project->id,
                'user_id' => auth()->id(),
                'assigned_to' => $this->assigned_to ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'status' => 'todo',
                'priority' => $this->priority,
                'due_date' => $this->due_date ?: null,
            ]);
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'create_task',
                'description' => 'Created task: ' . $this->title,
            ]);
            if ($this->assigned_to) {
                $task->assignedUser?->notify(new TaskAssignedNotification($task));
            }
        }
     

        $this->reset(['title', 'description', 'priority', 'due_date', 'assigned_to']);
        $this->priority = 'medium';
    }

    public function delete($id)
    {
        $task = Task::where('project_id', $this->project->id)
            ->findOrFail($id);

        if (auth()->user()->role !== 'manager') {
            abort(403);
        }

        $task->delete();
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->role === 'employee' && $this->project->user_id !== $user->manager_id) {
            abort(403);
        }

        $query = $this->project->tasks();

        if ($this->showMyTasks) {
            $query->where('assigned_to', auth()->id());
        }

        return view('livewire.tasks', [
            'tasks' => $query
                ->with(['comments.user', 'assignedUser', 'claimedByUser'])
                ->latest()
                ->get(),
        ])->layout('layouts.app');
    }
    public function updateStatus($taskId, $status)
    {
        $task = Task::where('project_id', $this->project->id)
            ->findOrFail($taskId);

        if ($task->status === 'done' && auth()->user()->role !== 'manager') {
            abort(403);
        }

        if (
            auth()->user()->role !== 'manager' &&
            $task->assigned_to !== auth()->id() &&
            $task->claimed_by !== auth()->id()
        ) {
            abort(403);
        }

        $task->update([
            'status' => $status,
        ]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_status',
            'description' => 'Changed task "' . $task->title . '" to ' . str_replace('_', ' ', $status),
        ]);
    }
    public function edit($taskId)
    {
        if (auth()->user()->role !== 'manager') {
            abort(403);
        }

        $task = Task::where('project_id', $this->project->id)
            ->findOrFail($taskId);

        $this->editingTaskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date;
        $this->assigned_to = $task->assigned_to;
        $this->buttonText = 'Update Task';
    }
    public function cancelEdit()
    {
        $this->reset(['title', 'description', 'priority', 'due_date', 'assigned_to', 'editingTaskId']);
        $this->priority = 'medium';
        $this->buttonText = 'Add Task';
    }
    public function addComment($taskId)
    {
        $this->validate([
            "commentText.$taskId" => 'required|string|max:1000',
        ]);

        $task = Task::where('project_id', $this->project->id)
            ->findOrFail($taskId);

        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'comment' => $this->commentText[$taskId],
        ]);

        $this->commentText[$taskId] = '';
    }
    public function takeTask($taskId)
    {
        $task = Task::where('project_id', $this->project->id)
            ->findOrFail($taskId);

        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            abort(403);
        }

        $task->update([
            'assigned_to' => auth()->id(),
            'claimed_by' => auth()->id(),
            'status' => 'in_progress',
        ]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'take_task',
            'description' => 'Took task: ' . $task->title,
        ]);
    }
    public function unfollowTask($taskId)
    {
        $task = Task::where('project_id', $this->project->id)
            ->findOrFail($taskId);

        if ($task->claimed_by !== auth()->id()) {
            abort(403);
        }

        $task->update([
            'assigned_to' => null,
            'claimed_by' => null,
            'status' => 'todo',
        ]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'unfollow_task',
            'description' => 'Unfollowed task: ' . $task->title,
        ]);
    }
   
}
