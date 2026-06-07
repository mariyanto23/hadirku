@props([
    'logoPath' => null,
    'label' => 'HadirKu',
])

@if($logoPath)
    <img
        src="{{ asset('storage/' . $logoPath) }}"
        alt="Logo {{ $label }}"
        {{ $attributes->class('object-contain') }}
    >
@else
    <svg
        {{ $attributes }}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
    >
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v10H4V6Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M9 13h6M6 20h12M9 16v4m6-4v4" />
    </svg>
@endif
