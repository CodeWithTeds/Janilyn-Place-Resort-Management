@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2 bg-brand-700 text-white rounded-md group'
            : 'flex items-center px-4 py-2 text-brand-100 hover:bg-brand-700 hover:text-white rounded-md group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
