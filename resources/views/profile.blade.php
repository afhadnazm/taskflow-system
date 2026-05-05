<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="bg-[#0f1115] py-8">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="rounded-xl border border-gray-800 bg-[#17191f] p-5 shadow-lg shadow-black/10 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="rounded-xl border border-red-500/30 bg-[#17191f] p-5 shadow-lg shadow-black/10 sm:p-8">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
