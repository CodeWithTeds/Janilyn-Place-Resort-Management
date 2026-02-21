@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md group'
            : 'flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-md group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
