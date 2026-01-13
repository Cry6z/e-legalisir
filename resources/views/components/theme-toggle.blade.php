@props([
    'variant' => 'ghost',
    'class' => null,
])

<button
    type="button"
    data-theme-toggle
    onclick="window.toggleTheme && window.toggleTheme()"
    {{ $attributes->merge([
        'class' => 'relative inline-flex h-8 w-16 items-center rounded-full border border-zinc-200/70 bg-white/70 px-2 text-zinc-500 shadow-sm ring-offset-2 transition hover:border-zinc-300 hover:bg-white dark:border-zinc-700/70 dark:bg-zinc-900/70 dark:text-zinc-300',
    ]) }}
    aria-pressed="false"
    aria-label="{{ __('Toggle theme') }}"
>
    <span class="pointer-events-none absolute inset-0 flex items-center justify-between px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-zinc-400 dark:text-zinc-500">
        <flux:icon name="sun" class="h-3.5 w-3.5 text-amber-400/70 dark:text-zinc-500" />
        <flux:icon name="moon" class="h-3.5 w-3.5 text-zinc-400 dark:text-amber-300/90" />
    </span>

    <span class="pointer-events-none absolute left-1 flex h-6 w-6 items-center justify-center rounded-full bg-white text-zinc-700 shadow-md transition-all duration-200 dark:translate-x-8 dark:bg-zinc-800 dark:text-amber-200">
        <flux:icon name="sun" class="h-3.5 w-3.5 dark:hidden" />
        <flux:icon name="moon" class="hidden h-3.5 w-3.5 dark:inline" />
    </span>

    <span class="sr-only">{{ __('Toggle theme') }}</span>
</button>
