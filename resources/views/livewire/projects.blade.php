<div wire:poll.2s class="mx-auto max-w-5xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Projects</h1>
    </div>

    @if ($successMessage)
        <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-emerald-300">
            {{ $successMessage }}
        </div>
    @endif

    <div class="mb-6 rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
        <div class="space-y-3">
            <input
                type="text"
                wire:model="name"
                placeholder="Project name"
                class="w-full rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >

            @error('name')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <textarea
                wire:model="description"
                placeholder="Project description"
                rows="3"
                class="w-full rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>

            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-2 font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:from-blue-400 hover:to-cyan-400 disabled:opacity-50"
            >
                <span wire:loading.remove>Create Project</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @forelse ($projects as $project)
            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10 transition duration-200 hover:border-blue-500">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">
                            {{ $project->name }}
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-gray-400">
                            {{ $project->description ?: 'No description' }}
                        </p>
                    </div>

                    <button
                        wire:click="delete({{ $project->id }})"
                        wire:confirm="Delete this project?"
                        class="text-sm font-medium text-red-400 transition hover:text-red-300"
                    >
                        Delete
                    </button>
                </div>

                <div class="mt-4">
                    <a
                        href="{{ route('projects.tasks', $project) }}"
                        class="inline-flex rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:from-blue-400 hover:to-cyan-400"
                    >
                        View Tasks
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-6 text-center text-gray-400 shadow-lg shadow-black/10 md:col-span-2">
                No projects yet.
            </div>
        @endforelse
    </div>
</div>
