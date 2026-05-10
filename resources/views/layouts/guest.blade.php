<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TaskFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#0f1115] font-sans text-gray-100 antialiased">
        <div class="flex min-h-screen items-center justify-center px-4 py-10">
            <div class="w-full" style="max-width: 480px;">
                <div class="mb-8 flex justify-center">
                    <a href="/" wire:navigate>
                        <img src="{{ asset('images/shams-logo.jpg') }}" alt="TaskFlow" class="h-20 w-auto rounded-xl object-contain">
                    </a>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-800 bg-[#17191f] p-6 shadow-2xl shadow-black/30 sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
