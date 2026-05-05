<x-app-layout>
    @php
        $tab = request('tab', 'dashboard');
        $currentUser = auth()->user();
        $projectOwnerId = $currentUser->role === 'manager' ? $currentUser->id : $currentUser->manager_id;

        $dashboardProjects = \App\Models\Project::with(['tasks.assignedUser'])
            ->where('user_id', $projectOwnerId)
            ->latest()
            ->get();

        $dashboardTasks = $dashboardProjects->flatMap->tasks;
        $ongoingTasksCount = $dashboardTasks->where('status', '!=', 'done')->count();
        $totalProjectsCount = $dashboardProjects->count();
        $totalMembersCount = \App\Models\User::where('id', $projectOwnerId)
            ->orWhere('manager_id', $projectOwnerId)
            ->count();

        $assigneeStats = $dashboardTasks
            ->groupBy(fn ($task) => optional($task->assignedUser)->name ?: 'Unassigned')
            ->map(function ($tasks, $name) {
                return [
                    'name' => $name,
                    'total' => $tasks->count(),
                    'ongoing' => $tasks->where('status', '!=', 'done')->count(),
                    'done' => $tasks->where('status', 'done')->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $ongoingProjects = $dashboardProjects
            ->filter(fn ($project) => $project->tasks->isEmpty() || $project->tasks->where('status', '!=', 'done')->isNotEmpty())
            ->values();

        $statCards = [
            [
                'title' => 'Ongoing Tasks',
                'value' => $ongoingTasksCount,
                'border' => 'border-blue-500/50',
                'badge' => 'bg-blue-500/15 text-blue-300 ring-blue-500/30',
                'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'title' => 'Total Projects',
                'value' => $totalProjectsCount,
                'border' => 'border-green-500/50',
                'badge' => 'bg-green-500/15 text-green-300 ring-green-500/30',
                'icon' => 'M3.75 9.75h16.5m-16.5 0A2.25 2.25 0 016 7.5h12a2.25 2.25 0 012.25 2.25m-16.5 0v6A2.25 2.25 0 006 18h12a2.25 2.25 0 002.25-2.25v-6',
            ],
            [
                'title' => 'Total Members',
                'value' => $totalMembersCount,
                'border' => 'border-orange-500/50',
                'badge' => 'bg-orange-500/15 text-orange-300 ring-orange-500/30',
                'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 3.375 3.375 0 003.375-3.375 3.375 3.375 0 00-3.375-3.375 9.38 9.38 0 00-2.625.372M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            ],
        ];
    @endphp

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-white">
            {{ ucfirst($tab) }}
        </h2>
    </x-slot>

    <div class="bg-[#0f1115] py-6">
        @if ($tab === 'dashboard')
            <div class="mx-auto max-w-7xl space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($statCards as $card)
                        <div class="rounded-xl border {{ $card['border'] }} bg-[#17191f] p-5 shadow-lg shadow-black/20">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-400">{{ $card['title'] }}</p>
                                    <p class="mt-3 text-3xl font-bold text-white">{{ $card['value'] }}</p>
                                </div>

                                <div class="flex h-11 w-11 items-center justify-center rounded-full ring-1 {{ $card['badge'] }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-6 shadow-lg shadow-black/20 xl:col-span-2">
                        <div class="mb-6 flex items-center justify-between gap-4">
                            <h3 class="text-lg font-semibold text-white">Project's Tasks Overview by Assignees</h3>
                            <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                                Live overview
                            </span>
                        </div>

                        @if ($assigneeStats->isEmpty())
                            <div class="flex min-h-64 items-center justify-center rounded-xl border border-gray-800 bg-[#0b0d12] text-sm font-medium text-gray-500">
                                No Data Found
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($assigneeStats as $assignee)
                                    @php
                                        $donePercent = $assignee['total'] > 0 ? round(($assignee['done'] / $assignee['total']) * 100) : 0;
                                    @endphp

                                    <div class="rounded-xl border border-gray-800 bg-[#0b0d12] p-4">
                                        <div class="mb-3 flex items-center justify-between gap-4">
                                            <div>
                                                <p class="font-semibold text-white">{{ $assignee['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $assignee['ongoing'] }} ongoing / {{ $assignee['total'] }} total</p>
                                            </div>
                                            <span class="text-sm font-semibold text-cyan-300">{{ $donePercent }}%</span>
                                        </div>

                                        <div class="h-2 overflow-hidden rounded-full bg-gray-800">
                                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-400" style="width: {{ $donePercent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-6 shadow-lg shadow-black/20">
                        <h3 class="mb-6 text-lg font-semibold text-white">Total Tasks by Assignee</h3>

                        @if ($assigneeStats->isEmpty())
                            <div class="flex min-h-64 items-center justify-center rounded-xl border border-gray-800 bg-[#0b0d12] text-sm font-medium text-gray-500">
                                No Data Found
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($assigneeStats as $assignee)
                                    @php
                                        $taskPercent = $dashboardTasks->count() > 0 ? round(($assignee['total'] / $dashboardTasks->count()) * 100) : 0;
                                    @endphp

                                    <div>
                                        <div class="mb-2 flex items-center justify-between text-sm">
                                            <span class="font-medium text-gray-300">{{ $assignee['name'] }}</span>
                                            <span class="font-semibold text-white">{{ $assignee['total'] }}</span>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-gray-800">
                                            <div class="h-full rounded-full bg-blue-500" style="width: {{ $taskPercent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-gray-800 bg-[#17191f] p-6 shadow-lg shadow-black/20">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-white">Ongoing Projects</h3>
                        <span class="text-sm text-gray-400">{{ $ongoingProjects->count() }} active</span>
                    </div>

                    @if ($ongoingProjects->isEmpty())
                        <div class="flex min-h-40 items-center justify-center rounded-xl border border-gray-800 bg-[#0b0d12] text-sm font-medium text-gray-500">
                            No Data Found
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($ongoingProjects as $project)
                                @php
                                    $projectTasksCount = $project->tasks->count();
                                    $projectDoneCount = $project->tasks->where('status', 'done')->count();
                                    $projectPercent = $projectTasksCount > 0 ? round(($projectDoneCount / $projectTasksCount) * 100) : 0;
                                @endphp

                                <a href="{{ route('projects.tasks', $project) }}" class="rounded-xl border border-gray-800 bg-[#0b0d12] p-4 transition hover:border-blue-500">
                                    <div class="mb-4 flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="font-semibold text-white">{{ $project->name }}</h4>
                                            <p class="mt-1 text-xs text-gray-500">{{ $projectTasksCount }} tasks</p>
                                        </div>
                                        <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                                            {{ $projectPercent }}%
                                        </span>
                                    </div>

                                    <div class="h-2 overflow-hidden rounded-full bg-gray-800">
                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-400" style="width: {{ $projectPercent }}%"></div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if ($tab === 'projects')
            @if (auth()->user()->role === 'manager')
                <livewire:projects />
            @else
                @php
                    $managerProjects = \App\Models\Project::where('user_id', auth()->user()->manager_id)
                        ->latest()
                        ->get();
                @endphp

                <div class="mx-auto max-w-5xl p-6">
                    <h2 class="mb-6 text-2xl font-bold text-white">
                        My Projects
                    </h2>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @forelse ($managerProjects as $project)
                            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg transition hover:border-blue-500">
                                <h3 class="text-lg font-bold text-white">
                                    {{ $project->name }}
                                </h3>

                                <p class="mt-2 text-sm text-gray-400">
                                    {{ $project->description ?: 'No description' }}
                                </p>

                                <div class="mt-4">
                                    <a
                                        href="{{ route('projects.tasks', $project) }}"
                                        class="inline-flex rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-3 py-2 text-sm font-semibold text-white"
                                    >
                                        View Tasks
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-6 text-center text-gray-400">
                                No projects available yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        @endif

        @if ($tab === 'meetings')
            <livewire:meetings />
        @endif

        @if ($tab === 'notifications')
            <div wire:poll.visible.5s class="mx-auto mt-6 max-w-5xl p-6">
                <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg">
                    <h3 class="mb-4 text-lg font-bold text-white">
                        Notifications
                    </h3>

                    @forelse (auth()->user()->unreadNotifications as $notification)
                        <a
                            href="{{ route('notifications.read', $notification->id) }}"
                            class="mb-2 block rounded-lg border border-blue-500/30 bg-blue-500/10 p-3 text-sm text-blue-200 hover:border-blue-500"
                        >
                            {{ $notification->data['message'] }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">
                            No new notifications.
                        </p>
                    @endforelse
                </div>
            </div>
        @endif

        @if ($tab === 'activity')
            @php
                $activityUser = auth()->user();

                if ($activityUser->role === 'admin') {
                    $activityLogs = \App\Models\ActivityLog::with('user')
                        ->latest()
                        ->take(20)
                        ->get();
                } elseif ($activityUser->role === 'manager') {
                    $activityLogs = \App\Models\ActivityLog::with('user')
                        ->whereHas('user', function ($query) use ($activityUser) {
                            $query->where('manager_id', $activityUser->id)
                                ->orWhere('id', $activityUser->id);
                        })
                        ->latest()
                        ->take(20)
                        ->get();
                } else {
                    $activityLogs = \App\Models\ActivityLog::with('user')
                        ->where(function ($query) use ($activityUser) {
                            $query->where('user_id', $activityUser->id)
                                ->orWhere('user_id', $activityUser->manager_id);
                        })
                        ->latest()
                        ->take(20)
                        ->get();
                }
            @endphp

            <div wire:poll.visible.5s class="mx-auto mt-6 max-w-5xl p-6">
                <div class="overflow-hidden rounded-xl border border-gray-800 bg-[#17191f] shadow-lg shadow-black/10">
                    <div class="border-b border-gray-800 px-6 py-5">
                        <div class="flex flex-col items-center gap-3 text-center">
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Activity Timeline
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Latest updates across tasks and projects.
                                </p>
                            </div>

                            <span class="inline-flex w-fit items-center rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                                Live updates
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse ($activityLogs as $log)
                                @php
                                    $actionStyles = [
                                        'create_task' => 'border-blue-500/30 bg-blue-500/10 text-blue-300',
                                        'update_status' => 'border-amber-500/30 bg-amber-500/10 text-amber-300',
                                        'take_task' => 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
                                        'unfollow_task' => 'border-red-500/30 bg-red-500/10 text-red-300',
                                    ];

                                    $actionLabels = [
                                        'create_task' => 'Created',
                                        'update_status' => 'Updated',
                                        'take_task' => 'Claimed',
                                        'unfollow_task' => 'Unfollowed',
                                    ];

                                    $badgeClass = $actionStyles[$log->action] ?? 'border-gray-700 bg-[#0b0d12] text-gray-300';
                                    $actionLabel = $actionLabels[$log->action] ?? str($log->action)->replace('_', ' ')->title();
                                    $initials = str($log->user?->name ?? 'User')
                                        ->explode(' ')
                                        ->map(fn ($part) => str($part)->substr(0, 1))
                                        ->take(2)
                                        ->implode('');
                                @endphp

                                <div class="relative rounded-xl border border-gray-800 bg-[#0b0d12] p-4 transition hover:border-blue-500/60">
                                    <div class="flex gap-4">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-cyan-500/30 bg-cyan-500/10 text-sm font-bold text-cyan-300">
                                            {{ $initials }}
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-white">
                                                        {{ $log->user?->name ?? 'Unknown user' }}
                                                    </p>
                                                    <p class="mt-1 text-sm leading-6 text-gray-300">
                                                        {{ $log->description }}
                                                    </p>
                                                </div>

                                                <span class="inline-flex w-fit shrink-0 rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                                                    {{ $actionLabel }}
                                                </span>
                                            </div>

                                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                                <span class="h-1 w-1 rounded-full bg-gray-700"></span>
                                                <span>{{ $log->created_at->format('d M Y, H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex min-h-52 flex-col items-center justify-center rounded-xl border border-gray-800 bg-[#0b0d12] px-6 text-center">
                                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full border border-gray-700 bg-[#17191f] text-cyan-300">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-white">
                                        No activity yet
                                    </p>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Task updates and project actions will appear here.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
