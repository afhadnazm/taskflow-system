<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskFlow') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{ mobileMenuOpen: false, notificationOpen: false }" class="bg-[#0f1115] font-sans text-slate-100 antialiased">
    @php
        $user = auth()->user();
        $notificationCount = $user ? $user->unreadNotifications()->count() : 0;
        $tab = request('tab', 'dashboard');

        $navItems = [
            ['label' => 'Dashboard', 'href' => route('dashboard') . '?tab=dashboard', 'active' => $tab === 'dashboard' && ! request()->routeIs('projects.*')],
            ['label' => 'Projects', 'href' => route('dashboard') . '?tab=projects', 'active' => $tab === 'projects' || request()->routeIs('projects.*')],
            ['label' => 'Meetings', 'href' => route('dashboard') . '?tab=meetings', 'active' => $tab === 'meetings'],
            ['label' => 'Activity', 'href' => route('dashboard') . '?tab=activity', 'active' => $tab === 'activity'],
        ];
    @endphp

    <div class="hidden" aria-hidden="true" id="taskflow-logout-proxy">
        <livewire:layout.navigation />
    </div>

    <div class="min-h-screen bg-[#0f1115]">
        <!-- Desktop Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-gray-800 bg-[#0f1115]/95 px-5 py-6 shadow-2xl shadow-black/30 lg:flex lg:flex-col">
            <a href="{{ route('dashboard') }}?tab=dashboard" wire:navigate class="block">
                <img
                    src="{{ asset('images/shams-logo.jpg') }}"
                    alt="TaskFlow"
                    class="h-auto w-auto rounded-lg object-contain"
                >
            </a>

            <div class="mt-8 rounded-2xl border border-gray-800 bg-[#17191f] p-4">
                <div class="text-sm font-semibold text-white">{{ $user->name }}</div>
                <div class="mt-1 text-xs font-medium uppercase tracking-[0.2em] text-slate-400">
                    {{ $user->role }}
                </div>
            </div>

            <nav class="mt-8 flex-1 space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" wire:navigate
                        class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold transition
                        {{ $item['active'] ? 'bg-cyan-400 text-slate-950' : 'text-gray-300 hover:bg-[#17191f] hover:text-white' }}">
                        <span>{{ $item['label'] }}</span>

                        @if ($item['label'] === 'Notifications' && $notificationCount > 0)
                            <span class="rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">
                                {{ $notificationCount }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-gray-800 pt-5">
                <a href="{{ route('profile') }}" wire:navigate
                    class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-300 hover:bg-[#17191f] hover:text-white">
                    Profile settings
                </a>

                <button type="button"
                    onclick="document.querySelector('#taskflow-logout-proxy button[wire\\:click=&quot;logout&quot;]')?.click()"
                    class="mt-2 w-full rounded-xl border border-gray-800 px-4 py-3 text-left text-sm font-semibold text-gray-300 hover:bg-rose-500/10 hover:text-rose-200">
                    Log out
                </button>

                <p class="mt-5 px-4 text-xs leading-5 text-gray-500">
                    Developed by <span class="font-semibold text-gray-300">afhad.barzani</span> and
                    <span class="font-semibold text-gray-300">sari.barzani</span>
                </p>
            </div>
        </aside>

        <!-- Mobile Overlay -->
        <div x-show="mobileMenuOpen" x-cloak @click="mobileMenuOpen = false"
            class="fixed inset-0 z-50 bg-black/60 lg:hidden"></div>

        <!-- Mobile Sidebar -->
        <aside x-show="mobileMenuOpen" x-cloak x-transition
            class="fixed left-0 top-0 z-50 h-screen w-72 border-r border-gray-800 bg-[#0f1115] p-5 lg:hidden">
            <div class="mb-8 flex items-center justify-between">
                <a href="{{ route('dashboard') }}?tab=dashboard" wire:navigate class="block">
                    <img
                        src="{{ asset('images/shams-logo.jpg') }}"
                        alt="TaskFlow"
                        class="h-12 w-auto rounded-lg object-contain"
                    >
                </a>

                <button @click="mobileMenuOpen = false" class="text-2xl text-white">
                    ×
                </button>
            </div>

            <nav class="flex h-[calc(100vh-130px)] flex-col justify-between">
                <div class="space-y-2">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}" wire:navigate
                            class="block rounded-xl px-4 py-3 text-sm font-semibold
                        {{ $item['active'] ? 'bg-cyan-400 text-slate-950' : 'text-gray-300 hover:bg-[#17191f] hover:text-white' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="space-y-2 border-t border-gray-800 pt-4">
                    <a href="{{ route('profile') }}" wire:navigate
                        class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-300 hover:bg-[#17191f] hover:text-white">
                        Profile settings
                    </a>

                    <button type="button"
                        onclick="document.querySelector('#taskflow-logout-proxy button[wire\\:click=&quot;logout&quot;]')?.click()"
                        class="w-full rounded-xl border border-gray-800 px-4 py-3 text-left text-sm font-semibold text-gray-300 hover:bg-rose-500/10 hover:text-rose-200">
                        Log out
                    </button>

                    <p class="px-4 pt-3 text-xs leading-5 text-gray-500">
                        Developed by <span class="font-semibold text-gray-300">afhad.barzani</span> and
                        <span class="font-semibold text-gray-300">sari.barzani</span>
                    </p>
                </div>
            </nav>
            </nav>
        </aside>

        <!-- Main Area -->
        <div class="lg:pl-72">
            <header class="sticky top-0 z-30 border-b border-gray-800 bg-[#0f1115]/90 backdrop-blur">
                <div class="flex min-h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
                    <button @click="mobileMenuOpen = true"
                        class="rounded-xl border border-gray-800 bg-[#17191f] px-4 py-3 text-white lg:hidden">
                        ☰
                    </button>


                    <div class="relative ml-auto max-w-xl flex-1">
                        <input type="search" placeholder="Search..."
                            class="w-full rounded-2xl border border-gray-800 bg-[#111318] px-4 py-3 text-sm text-gray-200 placeholder:text-slate-500 focus:border-cyan-300/70 focus:ring-2 focus:ring-cyan-400/20">
                    </div>

                    <div class="relative">
                        <button type="button" @click="notificationOpen = ! notificationOpen"
                            class="rounded-2xl border border-gray-800 bg-[#17191f] px-4 py-3 text-sm font-semibold text-gray-200 transition hover:border-cyan-400 hover:text-white">
                            Notifications
                            <span class="ml-2 rounded-full bg-cyan-400 px-2 py-0.5 text-xs font-bold text-slate-950">
                                {{ $notificationCount }}
                            </span>
                        </button>

                        <div
                            x-show="notificationOpen"
                            x-cloak
                            @click.outside="notificationOpen = false"
                            x-transition
                            class="absolute right-0 z-50 mt-3 w-80 overflow-hidden rounded-2xl border border-gray-800 bg-[#17191f] shadow-2xl shadow-black/40"
                        >
                            <div class="border-b border-gray-800 px-4 py-3">
                                <h3 class="text-sm font-semibold text-white">Notifications</h3>
                                <p class="mt-1 text-xs text-gray-500">{{ $notificationCount }} unread</p>
                            </div>

                            <div class="max-h-96 overflow-y-auto p-2">
                                @forelse (auth()->user()->unreadNotifications as $notification)
                                    <a
                                        href="{{ route('notifications.read', $notification->id) }}"
                                        class="block rounded-xl border border-transparent p-3 text-sm text-gray-300 transition hover:border-cyan-500/40 hover:bg-[#0b0d12] hover:text-white"
                                    >
                                        {{ $notification->data['message'] }}
                                    </a>
                                @empty
                                    <div class="flex min-h-28 items-center justify-center rounded-xl bg-[#0b0d12] px-4 text-center text-sm text-gray-500">
                                        No new notifications.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            @if (isset($header))
                <div class="border-b border-gray-800 bg-[#17191f]/50 px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endif

            <main class="min-h-[calc(100vh-5rem)] px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>
