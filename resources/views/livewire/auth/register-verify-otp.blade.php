<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Verifikasi OTP')"
            :description="__('Kami telah mengirim kode OTP ke :email. Masukkan kode tersebut untuk menyelesaikan pendaftaran.', ['email' => $email])"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.otp.verify') }}" class="flex flex-col gap-6">
            @csrf
            <label class="flex flex-col gap-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                {{ __('Kode OTP') }}
                <input
                    name="otp"
                    value="{{ old('otp') }}"
                    type="text"
                    required
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    placeholder="123456"
                    autofocus
                    class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-center text-xl tracking-[0.35em] text-zinc-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"
                    data-otp-input
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);"
                    onpaste="handleOtpPaste(event)"
                />
            </label>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 -mt-2">
                {{ __('Masukkan 6 digit angka sesuai yang kami kirim ke email Anda.') }}
            </p>
            @error('otp')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Verifikasi OTP') }}
            </flux:button>
        </form>

        <form method="POST" action="{{ route('register.otp.resend') }}">
            @csrf
            <flux:button
                type="submit"
                variant="subtle"
                class="w-full border border-zinc-200/70 bg-white/80 text-zinc-700 hover:bg-white dark:border-zinc-700/80 dark:bg-zinc-900/70 dark:text-zinc-100"
            >
                {{ __('Kirim Ulang OTP') }}
            </flux:button>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Ingin ubah email?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Kembali ke pendaftaran') }}</flux:link>
        </div>
    </div>
</x-layouts.auth>

@push('scripts')
    <script>
        window.handleOtpPaste = function (event) {
            event.preventDefault();
            const pasted = (event.clipboardData || window.clipboardData)
                .getData('text')
                .replace(/\D/g, '')
                .slice(0, 6);

            if (event.target && typeof event.target.value !== 'undefined') {
                event.target.value = pasted;
            }
        };
    </script>
@endpush
