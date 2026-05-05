<div wire:poll.2s class="mx-auto max-w-7xl p-6">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-cyan-400 hover:text-cyan-300 hover:underline">
            ← Back to Dashboard
        </a>

        <h1 class="mt-2 text-2xl font-bold text-white">
            {{ $project->name }}
        </h1>
    </div>

    @if (auth()->user()->role === 'manager')
        <div class="mb-6 rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
                <input type="text" wire:model="title" placeholder="Task title"
                    class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                <select wire:model="priority"
                    class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <input type="date" wire:model="due_date"
                    class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                <select wire:model="assigned_to"
                    class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Assign to</option>

                    @foreach (\App\Models\User::where('manager_id', auth()->id())->get() as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button wire:click="save" wire:loading.attr="disabled"
                        class="rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-2 font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:from-blue-400 hover:to-cyan-400 disabled:opacity-50">
                        <span wire:loading.remove>{{ $buttonText }}</span>
                        <span wire:loading>Saving...</span>
                    </button>

                    @if ($editingTaskId)
                        <button wire:click="cancelEdit"
                            class="rounded-lg border border-gray-700 bg-[#111318] px-4 py-2 font-semibold text-gray-300 transition hover:border-gray-600 hover:text-white">
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
            class="rounded-lg px-3 py-1 text-sm font-semibold transition {{ !$showMyTasks ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-950/20' : 'border border-gray-800 bg-[#17191f] text-gray-300 hover:border-blue-500 hover:text-white' }}">
            All Tasks
        </button>

        <button wire:click="$set('showMyTasks', true)"
            class="rounded-lg px-3 py-1 text-sm font-semibold transition {{ $showMyTasks ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-950/20' : 'border border-gray-800 bg-[#17191f] text-gray-300 hover:border-blue-500 hover:text-white' }}">
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
            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-4 shadow-lg shadow-black/10">
                <h2 class="mb-4 font-bold text-white">
                    {{ $statusLabel }}
                </h2>

                <div class="space-y-3">
                    @forelse ($tasks->where('status', $statusKey) as $task)
                        <div class="rounded-xl border border-gray-800 bg-[#111318] p-4 shadow-lg shadow-black/10 transition duration-200 hover:border-blue-500">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-white">
                                        {{ $task->title }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-400">
                                        Priority: {{ ucfirst($task->priority) }}
                                    </p>

                                    <p class="text-sm text-gray-400">
                                        Due: {{ $task->due_date ?? 'No date' }}
                                    </p>

                                    @if (!$task->assigned_to)
                                        <button wire:click="takeTask({{ $task->id }})"
                                            class="mt-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-2 py-1 text-xs font-semibold text-white">
                                            Take Task
                                        </button>
                                    @elseif ($task->assigned_to === auth()->id())
                                        <p class="mt-2 text-sm text-gray-400">
                                            Following by: {{ $task->assignedUser->name }}
                                        </p>
                                    @else
                                        <p class="mt-2 text-sm text-gray-400">
                                            Assigned to: {{ $task->assignedUser->name }}
                                        </p>
                                    @endif
                                </div>
                                @if ($task->claimed_by === auth()->id() && $task->status !== 'done')
                                    <button wire:click="unfollowTask({{ $task->id }})"
                                        wire:confirm="Are you sure you want to unfollow this task?"
                                        class="mt-2 rounded-lg border border-red-500/30 bg-red-500/10 px-2 py-1 text-xs font-semibold text-red-300 hover:border-red-500">
                                        Unfollow
                                    </button>
                                @endif

                                @if (auth()->user()->role === 'manager')
                                    <div class="flex gap-3">
                                        <button wire:click="edit({{ $task->id }})"
                                            class="text-sm font-medium text-blue-400 hover:text-blue-300">
                                            Edit
                                        </button>

                                        <button wire:click="delete({{ $task->id }})"
                                            class="text-sm font-medium text-red-400 hover:text-red-300">
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
                                            class="rounded-lg border border-gray-700 bg-[#17191f] px-2 py-1 text-xs font-semibold text-gray-300 hover:border-blue-500 hover:text-white">
                                            To Do
                                        </button>
                                    @endif

                                    @if ($task->status !== 'in_progress')
                                        <button wire:click="updateStatus({{ $task->id }}, 'in_progress')"
                                            class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-2 py-1 text-xs font-semibold text-amber-200 hover:border-amber-500">
                                            Progress
                                        </button>
                                    @endif

                                    @if ($task->status !== 'done')
                                        <button wire:click="updateStatus({{ $task->id }}, 'done')"
                                            wire:confirm="Are you sure this task is done? You cannot change it after confirming."
                                            class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-2 py-1 text-xs font-semibold text-emerald-200 hover:border-emerald-500">
                                            Done
                                        </button>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4 border-t border-gray-800 pt-3">
                                <h4 class="mb-2 text-sm font-semibold text-gray-300">
                                    Comments
                                </h4>

                                <div class="space-y-2">
                                    @forelse ($task->comments as $comment)
                                        <div class="rounded-lg border border-gray-800 bg-[#17191f] p-2 text-sm">
                                            <p class="font-semibold text-white">
                                                {{ $comment->user->name }}
                                            </p>

                                            <p class="text-gray-400">
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
                                        class="w-full rounded-lg border-gray-700 bg-[#111318] text-sm text-gray-200 placeholder:text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                    <button wire:click="addComment({{ $task->id }})"
                                        class="rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-3 py-1 text-sm font-semibold text-white hover:from-blue-400 hover:to-cyan-400">
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
