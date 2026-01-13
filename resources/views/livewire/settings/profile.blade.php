<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your information and profile photo')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            @if ($managesPhoto)
                <div class="rounded-2xl border border-dashed border-zinc-300/70 p-4 dark:border-zinc-700/70">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="relative h-20 w-20 overflow-hidden rounded-xl border border-zinc-200/60 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900">
                                @php
                                    $preview = $photo ? $photo->temporaryUrl() : $currentPhotoUrl;
                                @endphp

                                @if ($preview)
                                    <img
                                        src="{{ $preview }}"
                                        alt="{{ __('Profile photo preview') }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-2xl font-semibold uppercase text-zinc-500 dark:text-zinc-400">
                                        {{ strtoupper(Str::substr($name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ __('Profile Photo') }}</p>
                                <p>{{ __('PNG atau JPG, maks 2MB.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 sm:w-1/2">
                            <label class="flex flex-col gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                {{ __('Upload photo baru') }}
                                <input
                                    type="file"
                                    accept="image/*"
                                    wire:model="photo"
                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm text-zinc-700 file:me-4 file:rounded-lg file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:uppercase file:tracking-[0.15em] file:text-white dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-200"
                                >
                            </label>

                            @error('photo')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror

                            <div class="flex flex-wrap gap-2">
                                <flux:button type="submit" variant="primary" class="flex-1 justify-center">
                                    {{ __('Simpan Perubahan') }}
                                </flux:button>

                                @if ($currentPhotoUrl || $photo)
                                    <flux:button type="button" variant="ghost" class="flex-1 justify-center" wire:click="removeProfilePhoto">
                                        {{ __('Hapus Foto') }}
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
