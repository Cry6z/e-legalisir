<x-layouts.auth>
    <div class="flex flex-col gap-6 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-xl dark:border-white/5 dark:bg-white/5">
        <div class="space-y-3 text-center">
            <span class="inline-flex items-center justify-center rounded-full border border-emerald-200/60 bg-emerald-50/70 px-4 py-1 text-xs font-semibold uppercase tracking-[0.35em] text-emerald-600 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                {{ __('E-Legalisir') }}
            </span>
            <x-auth-header
                :title="__('Buat Akun Alumni')"
                :description="__('Isi data Anda untuk menerima kode OTP verifikasi melalui email.')"
            />
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <p class="rounded-2xl border border-dashed border-emerald-200/70 bg-emerald-50/70 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100">
            {{ __('Kode OTP berlaku 10 menit. Pastikan email aktif dan jangan bagikan kode kepada siapa pun.') }}
        </p>

        <form method="POST" action="{{ route('register.otp.request') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="name"
                :label="__('Nama Lengkap')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('Masukkan nama lengkap Anda') }}"
            />

            <flux:input
                name="email"
                :label="__('Email Aktif')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@contoh.com"
            />

            <flux:input
                name="password"
                :label="__('Kata Sandi')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Minimal 8 karakter') }}"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Konfirmasi Kata Sandi')"
                type="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Ulangi kata sandi') }}"
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full py-3 text-base font-semibold">
                {{ __('Kirim OTP & Daftar') }}
            </flux:button>
        </form>

        <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-xs text-zinc-200 dark:border-white/5 dark:bg-white/5 dark:text-zinc-300">
            <p class="font-semibold text-white">{{ __('Tips cepat:') }}</p>
            <ul class="mt-2 space-y-1">
                <li>• {{ __('Gunakan email pribadi yang mudah diakses.') }}</li>
                <li>• {{ __('Cek folder spam jika OTP belum masuk.') }}</li>
                <li>• {{ __('Catat kata sandi Anda di tempat aman.') }}</li>
            </ul>
        </div>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-100 dark:text-zinc-300">
            <span>{{ __('Sudah punya akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate class="font-semibold text-emerald-400 hover:text-emerald-300">
                {{ __('Masuk di sini') }}
            </flux:link>
        </div>
    </div>
</x-layouts.auth>
