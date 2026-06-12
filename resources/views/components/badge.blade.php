{{-- resources/views/components/badge.blade.php --}}
@props([
    'type' => 'default',  {{-- success, danger, warning, info, purple, default --}}
    'dot' => false,         {{-- show dot indicator --}}
])

@php
    $styles = [
        'success' => 'bg-emerald-600 text-white border-emerald-600',
        'danger'  => 'bg-rose-600 text-white border-rose-600',
        'warning' => 'bg-amber-500 text-white border-amber-500',
        'info'    => 'bg-sky-600 text-white border-sky-600',
        'purple'  => 'bg-violet-600 text-white border-violet-600',
        'default' => 'bg-gray-600 text-white border-gray-600',
    ];

    $dotStyles = [
        'success' => 'bg-white',
        'danger'  => 'bg-white',
        'warning' => 'bg-white',
        'info'    => 'bg-white',
        'purple'  => 'bg-white',
        'default' => 'bg-white',
    ];

    $classes = $styles[$type] ?? $styles['default'];
    $dotClass = $dotStyles[$type] ?? $dotStyles['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded border $classes"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
    @endif
    {{ $slot }}
</span>
