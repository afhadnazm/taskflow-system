<div class="mx-auto max-w-7xl space-y-6 p-6">
    <div>
        <h1 class="text-2xl font-bold text-white">Manage Users</h1>
        <p class="mt-1 text-sm text-gray-500">Create users, assign roles, and connect employees to managers.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-purple-500/40 bg-[#17191f] p-5">
            <p class="text-sm text-gray-400">Admins</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $adminsCount }}</p>
        </div>
        <div class="rounded-xl border border-blue-500/40 bg-[#17191f] p-5">
            <p class="text-sm text-gray-400">Managers</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $managersCount }}</p>
        </div>
        <div class="rounded-xl border border-emerald-500/40 bg-[#17191f] p-5">
            <p class="text-sm text-gray-400">Employees</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $employeesCount }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
        <h2 class="mb-4 text-lg font-bold text-white">{{ $editingUserId ? 'Edit User' : 'Create User' }}</h2>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-5">
            <input wire:model="name" type="text" placeholder="Name" class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500">
            <input wire:model="email" type="email" placeholder="Email" class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500">
            <input wire:model="password" type="password" placeholder="{{ $editingUserId ? 'New password (optional)' : 'Password' }}" class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500">
            <select wire:model.live="role" class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 focus:border-blue-500 focus:ring-blue-500">
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="employee">Employee</option>
            </select>
            <select wire:model="manager_id" @disabled($role !== 'employee') class="rounded-lg border-gray-700 bg-[#111318] text-gray-200 disabled:opacity-50 focus:border-blue-500 focus:ring-blue-500">
                <option value="">No manager</option>
                @foreach ($managers as $manager)
                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-red-300 md:grid-cols-3">
            @error('name') <p>{{ $message }}</p> @enderror
            @error('email') <p>{{ $message }}</p> @enderror
            @error('password') <p>{{ $message }}</p> @enderror
            @error('role') <p>{{ $message }}</p> @enderror
            @error('manager_id') <p>{{ $message }}</p> @enderror
        </div>

        <div class="mt-4 flex gap-3">
            <button wire:click="save" class="rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-2 text-sm font-semibold text-white">
                {{ $editingUserId ? 'Update User' : 'Create User' }}
            </button>
            @if ($editingUserId)
                <button wire:click="resetForm" class="rounded-lg border border-gray-700 bg-[#111318] px-4 py-2 text-sm font-semibold text-gray-300 hover:text-white">
                    Cancel
                </button>
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="text-lg font-bold text-white">All Users</h2>
            <input
                wire:model.live.debounce.500ms="search"
                type="search"
                placeholder="Search users..."
                class="w-full rounded-xl border border-gray-800 bg-[#0f1115] px-4 py-2 text-white placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500 md:max-w-sm"
            >
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-3 py-3">Name</th>
                        <th class="px-3 py-3">Email</th>
                        <th class="px-3 py-3">Role</th>
                        <th class="px-3 py-3">Manager</th>
                        <th class="px-3 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 text-gray-300">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-3 py-4 font-semibold text-white">{{ $user->name }}</td>
                            <td class="px-3 py-4">{{ $user->email }}</td>
                            <td class="px-3 py-4">{{ $user->role }}</td>
                            <td class="px-3 py-4">{{ $user->manager?->name ?? '-' }}</td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end gap-3">
                                    <button wire:click="edit({{ $user->id }})" class="text-blue-300 hover:text-blue-200">Edit</button>
                                    <button wire:click="delete({{ $user->id }})" wire:confirm="Delete this user?" @disabled(auth()->id() === $user->id) class="text-red-300 hover:text-red-200 disabled:cursor-not-allowed disabled:opacity-40">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
