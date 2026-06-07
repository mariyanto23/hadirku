@props([
    'variant' => 'light',
])

@php
    $isHero = $variant === 'hero';
    $clockClasses = $isHero
        ? 'inline-flex items-center gap-2 rounded-2xl bg-white/20 px-4 py-2 text-sm font-bold text-white backdrop-blur'
        : 'inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-bold text-slate-700 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-200';
@endphp

<div
    x-data="{
        text: @js(now('Asia/Jakarta')->translatedFormat('l, d F Y - H:i') . ' WIB'),
        updateClock() {
            const parts = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }).formatToParts(new Date()).reduce((carry, part) => {
                carry[part.type] = part.value;
                return carry;
            }, {});

            const hour = String(parts.hour).padStart(2, '0');
            const minute = String(parts.minute).padStart(2, '0');

            this.text = `${parts.weekday}, ${parts.day} ${parts.month} ${parts.year} - ${hour}:${minute} WIB`;
        },
    }"
    x-init="updateClock(); setInterval(() => updateClock(), 1000)"
    {{ $attributes->merge(['class' => $clockClasses]) }}
>
    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>

    <span x-text="text">
        {{ now('Asia/Jakarta')->translatedFormat('l, d F Y - H:i') }} WIB
    </span>
</div>
