<div class="mx-auto max-w-7xl space-y-6 p-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Manage Projects</h1>
        <p class="mt-1 text-sm text-gray-500">View all projects across every manager.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @forelse ($projects as $project)
            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10 transition hover:border-blue-500">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ $project->name }}</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-400">{{ $project->description ?: 'No description' }}</p>
                    </div>
                    <button wire:click="delete({{ $project->id }})" wire:confirm="Delete this project?" class="text-sm font-semibold text-red-300 hover:text-red-200">
                        Delete
                    </button>
                </div>

                <div class="mt-5 flex flex-wrap gap-2 text-xs">
                    <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 font-semibold text-cyan-300">Owner: {{ $project->user?->name ?? 'Unknown' }}</span>
                    <span class="rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1 font-semibold text-blue-300">{{ $project->tasks->count() }} tasks</span>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-8 text-center text-gray-500 lg:col-span-2">
                No projects found.
            </div>
        @endforelse
    </div>
</div>
