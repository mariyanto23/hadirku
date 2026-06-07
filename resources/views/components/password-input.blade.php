@props([
    'toggleLabel' => 'Tampilkan atau sembunyikan kata sandi',
])

<div x-data="{ passwordVisible: false }" class="relative">
    <input
        x-bind:type="passwordVisible ? 'text' : 'password'"
        {{ $attributes->class('hk-input pr-12') }}
    >

    <button
        type="button"
        x-on:click="passwordVisible = ! passwordVisible"
        class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 transition hover:text-blue-600 dark:text-slate-500 dark:hover:text-blue-300"
        :aria-label="passwordVisible ? 'Sembunyikan kata sandi' : '{{ $toggleLabel }}'"
        :title="passwordVisible ? 'Sembunyikan kata sandi' : '{{ $toggleLabel }}'"
    >
        <svg
            x-show="!passwordVisible"
            class="h-5 w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12S6 5 12 5s9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
        </svg>

        <svg
            x-show="passwordVisible"
            class="h-5 w-5"
            x-cloak
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.3A9.4 9.4 0 0 1 12 5c6 0 9.5 7 9.5 7a16.3 16.3 0 0 1-3.1 4.1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.8A16.2 16.2 0 0 0 2.5 12S6 19 12 19a9 9 0 0 0 4-.9" />
        </svg>
    </button>
</div>
