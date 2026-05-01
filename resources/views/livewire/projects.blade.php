<div class="mx-auto max-w-5xl p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
    </div>

    @if ($successMessage)
        <div class="mb-4 rounded bg-green-100 p-3 text-green-700">
            {{ $successMessage }}
        </div>
    @endif

    <div class="mb-6 rounded-lg bg-white p-5 shadow">
        <div class="space-y-3">
            <input
                type="text"
                wire:model="name"
                placeholder="Project name"
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >

            @error('name')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror

            <textarea
                wire:model="description"
                placeholder="Project description"
                rows="3"
                class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            ></textarea>

            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
            >
                <span wire:loading.remove>Create Project</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @forelse ($projects as $project)
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ $project->name }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $project->description ?: 'No description' }}
                        </p>
                    </div>

                    <button
                        wire:click="delete({{ $project->id }})"
                        wire:confirm="Delete this project?"
                        class="text-sm text-red-500 hover:text-red-700"
                    >
                        Delete
                    </button>
                </div>

                <div class="mt-4">
                    <a
                        href="{{ route('projects.tasks', $project) }}"
                        class="inline-flex rounded bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-700"
                    >
                        View Tasks
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-lg bg-white p-6 text-center text-gray-500 shadow md:col-span-2">
                No projects yet.
            </div>
        @endforelse
    </div>
</div>