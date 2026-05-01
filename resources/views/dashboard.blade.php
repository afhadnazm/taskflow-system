<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        @if (auth()->user()->role === 'manager')
            <livewire:dashboard-stats />
            <livewire:projects />
        @else
            @php
                $managerProject = \App\Models\Project::where('user_id', auth()->user()->manager_id)->first();
            @endphp

            <div class="mx-auto max-w-5xl p-6">
                <h2 class="mb-4 text-xl font-bold">
                    My Work
                </h2>

                @if ($managerProject)
                    <a
                        href="{{ route('projects.tasks', $managerProject) }}"
                        class="rounded bg-blue-600 px-4 py-2 text-white"
                    >
                        Go to Tasks
                    </a>
                @else
                    <p class="text-gray-500">
                        No tasks available yet. Your manager has not created a project.
                    </p>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>