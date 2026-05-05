<div class="mx-auto max-w-5xl space-y-6 p-6">
    @if(auth()->user()->role === 'manager')
        <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-white">Create Meeting</h2>
                <p class="mt-1 text-sm text-gray-500">Schedule a meeting for your team.</p>
            </div>

            <div class="space-y-3">
                <input
                    wire:model="title"
                    placeholder="Title"
                    class="w-full rounded-lg border-gray-700 bg-[#111318] px-3 py-2 text-gray-200 placeholder:text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                <textarea
                    wire:model="description"
                    placeholder="Description"
                    rows="3"
                    class="w-full rounded-lg border-gray-700 bg-[#111318] px-3 py-2 text-gray-200 placeholder:text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                ></textarea>

                <input
                    type="datetime-local"
                    wire:model="meeting_time"
                    class="w-full rounded-lg border-gray-700 bg-[#111318] px-3 py-2 text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                <button
                    wire:click="create"
                    class="rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-2 font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:from-blue-400 hover:to-cyan-400"
                >
                    Create Meeting
                </button>
            </div>
        </div>
    @endif

    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-white">Meetings</h2>
                <p class="mt-1 text-sm text-gray-500">Upcoming team meetings and schedules.</p>
            </div>

            <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                {{ $meetings->count() }} total
            </span>
        </div>

        <div class="space-y-3">
            @forelse($meetings as $meeting)
                <div class="rounded-xl border border-gray-800 bg-[#0b0d12] p-4 transition hover:border-blue-500">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="font-bold text-white">{{ $meeting->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-400">
                                {{ $meeting->description ?: 'No description' }}
                            </p>
                        </div>

                        <div class="shrink-0 rounded-lg border border-cyan-500/30 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-300">
                            {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('d M Y - H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex min-h-32 items-center justify-center rounded-xl border border-gray-800 bg-[#0b0d12] text-center text-sm text-gray-500">
                    No meetings yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
