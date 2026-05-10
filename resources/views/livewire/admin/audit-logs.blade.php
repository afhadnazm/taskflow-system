<div class="mx-auto max-w-7xl space-y-6 p-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Audit Logs</h1>
        <p class="mt-1 text-sm text-gray-500">Security and system-level changes across TaskFlow.</p>
    </div>

    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search actor, target, or description..."
                class="md:col-span-2 rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500"
            >

            <select
                wire:model.live="action"
                class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">All actions</option>
                @foreach ($actions as $actionName)
                    <option value="{{ $actionName }}">{{ str($actionName)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-800 bg-[#17191f] shadow-lg shadow-black/10">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-4">Date</th>
                        <th class="px-4 py-4">Actor</th>
                        <th class="px-4 py-4">Action</th>
                        <th class="px-4 py-4">Description</th>
                        <th class="px-4 py-4">Target User</th>
                        <th class="px-4 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-gray-300">
                    @forelse ($logs as $log)
                        <tr class="align-top transition hover:bg-[#0b0d12]">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-medium text-white">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-white">{{ $log->actor?->name ?? 'System' }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $log->actor?->email }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                                    {{ str($log->action)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td class="px-4 py-4 max-w-md text-gray-300">
                                {{ $log->description ?: '-' }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-white">{{ $log->targetUser?->name ?? '-' }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $log->targetUser?->email }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-gray-400">{{ $log->ip_address ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                No audit logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
