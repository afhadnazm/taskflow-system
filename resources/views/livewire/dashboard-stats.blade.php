<div wire:poll.2s class="mx-auto mb-6 max-w-5xl px-6">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <p class="text-sm font-medium text-gray-400">Projects</p>
            <h2 class="mt-2 text-3xl font-bold text-white">
                {{ $projectsCount }}
            </h2>
        </div>

        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <p class="text-sm font-medium text-gray-400">Total Tasks</p>
            <h2 class="mt-2 text-3xl font-bold text-white">
                {{ $totalTasks }}
            </h2>
        </div>

        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <p class="text-sm font-medium text-gray-400">My Tasks</p>
            <h2 class="mt-2 text-3xl font-bold text-white">
                {{ $myTasks }}
            </h2>
        </div>

        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <p class="text-sm font-medium text-gray-400">To Do</p>
            <h2 class="mt-2 text-3xl font-bold text-white">
                {{ $todoTasks }}
            </h2>
        </div>

        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <p class="text-sm font-medium text-gray-400">In Progress</p>
            <h2 class="mt-2 text-3xl font-bold text-white">
                {{ $progressTasks }}
            </h2>
        </div>

        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <p class="text-sm font-medium text-gray-400">Done</p>
            <h2 class="mt-2 text-3xl font-bold text-white">
                {{ $doneTasks }}
            </h2>
        </div>
    </div>
</div>
