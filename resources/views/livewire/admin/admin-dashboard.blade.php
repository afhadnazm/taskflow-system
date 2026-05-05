<div class="mx-auto max-w-7xl space-y-6 p-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">System-wide overview for TaskFlow.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
        @foreach ([
            ['label' => 'Users', 'value' => $usersCount, 'color' => 'border-cyan-500/50 text-cyan-300'],
            ['label' => 'Admins', 'value' => $adminsCount, 'color' => 'border-purple-500/50 text-purple-300'],
            ['label' => 'Managers', 'value' => $managersCount, 'color' => 'border-blue-500/50 text-blue-300'],
            ['label' => 'Employees', 'value' => $employeesCount, 'color' => 'border-emerald-500/50 text-emerald-300'],
            ['label' => 'Projects', 'value' => $projectsCount, 'color' => 'border-orange-500/50 text-orange-300'],
            ['label' => 'Tasks', 'value' => $tasksCount, 'color' => 'border-amber-500/50 text-amber-300'],
        ] as $card)
            <div class="rounded-xl border {{ $card['color'] }} bg-[#17191f] p-5 shadow-lg shadow-black/10">
                <p class="text-sm font-medium text-gray-400">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-bold text-white">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <h2 class="mb-4 text-lg font-bold text-white">Recent Users</h2>
            <div class="space-y-3">
                @forelse ($recentUsers as $user)
                    <div class="rounded-xl border border-gray-800 bg-[#0b0d12] p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-white">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-gray-800 bg-[#0b0d12] p-6 text-center text-sm text-gray-500">No users found.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <h2 class="mb-4 text-lg font-bold text-white">Recent Tasks</h2>
            <div class="space-y-3">
                @forelse ($recentTasks as $task)
                    <div class="rounded-xl border border-gray-800 bg-[#0b0d12] p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-white">{{ $task->title }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $task->project?->name ?? 'No project' }}</p>
                            </div>
                            <span class="rounded-full border border-blue-500/30 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-300">
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-gray-800 bg-[#0b0d12] p-6 text-center text-sm text-gray-500">No tasks found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
