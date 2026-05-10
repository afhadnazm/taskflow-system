<div class="mx-auto max-w-7xl space-y-6 p-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Manage Tasks</h1>
        <p class="mt-1 text-sm text-gray-500">View all tasks across all projects.</p>
    </div>

    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-lg font-bold text-white">All Tasks</h2>
            <input
                wire:model.live.debounce.500ms="search"
                type="search"
                placeholder="Search tasks..."
                class="w-full rounded-xl border border-gray-800 bg-[#0f1115] px-4 py-2 text-white placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500 md:max-w-sm"
            >
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-3 py-3">Task</th>
                        <th class="px-3 py-3">Project</th>
                        <th class="px-3 py-3">Owner</th>
                        <th class="px-3 py-3">Assigned</th>
                        <th class="px-3 py-3">Priority</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-gray-300">
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="px-3 py-4 font-semibold text-white">{{ $task->title }}</td>
                            <td class="px-3 py-4">{{ $task->project?->name ?? '-' }}</td>
                            <td class="px-3 py-4">{{ $task->project?->user?->name ?? '-' }}</td>
                            <td class="px-3 py-4">{{ $task->assignedUser?->name ?? 'Unassigned' }}</td>
                            <td class="px-3 py-4">{{ ucfirst($task->priority) }}</td>
                            <td class="px-3 py-4">
                                <span class="rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-300">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <button wire:click="delete({{ $task->id }})" wire:confirm="Delete this task?" class="text-sm font-semibold text-red-300 hover:text-red-200">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-500">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
