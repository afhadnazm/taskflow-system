<div class="mx-auto mb-6 max-w-5xl px-6">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Projects</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $projectsCount }}
            </h2>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Total Tasks</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $totalTasks }}
            </h2>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">My Tasks</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $myTasks }}
            </h2>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">To Do</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $todoTasks }}
            </h2>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">In Progress</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $progressTasks }}
            </h2>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Done</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">
                {{ $doneTasks }}
            </h2>
        </div>
    </div>
</div>