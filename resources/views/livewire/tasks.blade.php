<div class="mx-auto max-w-7xl p-6">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>

        <h1 class="mt-2 text-2xl font-bold text-gray-900">
            {{ $project->name }}
        </h1>
    </div>

    @if (auth()->user()->role === 'manager')
        <div class="mb-6 rounded-lg bg-white p-5 shadow">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
                <input type="text" wire:model="title" placeholder="Task title"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                <select wire:model="priority"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <input type="date" wire:model="due_date"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                <select wire:model="assigned_to"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Assign to</option>

                    @foreach (\App\Models\User::where('manager_id', auth()->id())->get() as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove>{{ $buttonText }}</span>
                        <span wire:loading>Saving...</span>
                    </button>

                    @if ($editingTaskId)
                        <button wire:click="cancelEdit"
                            class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                            Cancel
                        </button>
                    @endif
                </div>
            </div>

            @error('title')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="mb-4 flex gap-2">
        <button wire:click="$set('showMyTasks', false)"
            class="rounded px-3 py-1 {{ !$showMyTasks ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            All Tasks
        </button>

        <button wire:click="$set('showMyTasks', true)"
            class="rounded px-3 py-1 {{ $showMyTasks ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
            My Tasks
        </button>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        @php
            $columns = [
                'todo' => 'To Do',
                'in_progress' => 'In Progress',
                'done' => 'Done',
            ];
        @endphp

        @foreach ($columns as $statusKey => $statusLabel)
            <div class="rounded-lg bg-gray-100 p-4">
                <h2 class="mb-4 font-bold text-gray-700">
                    {{ $statusLabel }}
                </h2>

                <div class="space-y-3">
                    @forelse ($tasks->where('status', $statusKey) as $task)
                        <div class="rounded-lg bg-white p-4 shadow">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $task->title }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Priority: {{ ucfirst($task->priority) }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        Due: {{ $task->due_date ?? 'No date' }}
                                    </p>

                                    @if (!$task->assigned_to)
                                        <button wire:click="takeTask({{ $task->id }})"
                                            class="mt-2 rounded bg-blue-500 px-2 py-1 text-xs text-white">
                                            Take Task
                                        </button>
                                    @elseif ($task->assigned_to === auth()->id())
                                        <p class="mt-2 text-sm text-gray-500">
                                            Following by: {{ $task->assignedUser->name }}
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm text-gray-500">
                                            Assigned to: {{ $task->assignedUser->name }}
                                        </p>
                                    @endif
                                </div>

                                @if (auth()->user()->role === 'manager')
                                    <div class="flex gap-3">
                                        <button wire:click="edit({{ $task->id }})"
                                            class="text-sm text-blue-500 hover:text-blue-700">
                                            Edit
                                        </button>

                                        <button wire:click="delete({{ $task->id }})"
                                            class="text-sm text-red-500 hover:text-red-700">
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if (auth()->user()->role === 'manager' ||
                                    (($task->assigned_to === auth()->id() || $task->claimed_by === auth()->id()) && $task->status !== 'done'))
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if ($task->status !== 'todo')
                                        <button wire:click="updateStatus({{ $task->id }}, 'todo')"
                                            class="rounded bg-gray-200 px-2 py-1 text-xs">
                                            To Do
                                        </button>
                                    @endif

                                    @if ($task->status !== 'in_progress')
                                        <button wire:click="updateStatus({{ $task->id }}, 'in_progress')"
                                            class="rounded bg-yellow-200 px-2 py-1 text-xs">
                                            Progress
                                        </button>
                                    @endif

                                    @if ($task->status !== 'done')
                                        <button wire:click="updateStatus({{ $task->id }}, 'done')"
                                            wire:confirm="Are you sure this task is done? You cannot change it after confirming."
                                            class="rounded bg-green-200 px-2 py-1 text-xs">
                                            Done
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4 border-t pt-3">
                                <h4 class="mb-2 text-sm font-semibold text-gray-700">
                                    Comments
                                </h4>

                                <div class="space-y-2">
                                    @forelse ($task->comments as $comment)
                                        <div class="rounded bg-gray-50 p-2 text-sm">
                                            <p class="font-semibold text-gray-700">
                                                {{ $comment->user->name }}
                                            </p>

                                            <p class="text-gray-600">
                                                {{ $comment->comment }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400">No comments yet.</p>
                                    @endforelse
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <input type="text" wire:model="commentText.{{ $task->id }}"
                                        placeholder="Write comment..."
                                        class="w-full rounded border-gray-300 text-sm shadow-sm">

                                    <button wire:click="addComment({{ $task->id }})"
                                        class="rounded bg-gray-900 px-3 py-1 text-sm text-white hover:bg-gray-700">
                                        Send
                                    </button>
                                </div>

                                @error("commentText.$task->id")
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No tasks.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
