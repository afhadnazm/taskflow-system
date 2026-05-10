<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $defaultRoute = auth()->user()->role === 'admin'
            ? route('admin.dashboard', absolute: false)
            : route('dashboard', absolute: false);

        $this->redirectIntended(default: $defaultRoute, navigate: true);
    }
}; ?>

<div>
    <div class="mb-8 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">TaskFlow</p>
        <h1 class="mt-3 text-2xl font-bold text-white">Sign in to your workspace</h1>
        <p class="mt-2 text-sm text-gray-500">Use the account created by your administrator.</p>
    </div>

    <form wire:submit="login">
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input wire:model="form.email" id="email" class="mt-2 block w-full rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500" type="email" name="email"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" class="text-gray-300" />

            <x-text-input wire:model="form.password" id="password" class="mt-2 block w-full rounded-lg border-gray-700 bg-[#111318] text-gray-200 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500" type="password"
                name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="mt-5 flex items-center justify-between gap-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="rounded border-gray-700 bg-[#111318] text-cyan-500 shadow-sm focus:ring-cyan-500" name="remember">
                <span class="ms-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="rounded-md text-sm text-cyan-300 hover:text-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/20 transition hover:from-blue-400 hover:to-cyan-400">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</div>
