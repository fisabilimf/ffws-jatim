@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'loading' => false,
    'class' => ''
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        'info' => 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-400',
        'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
        'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-500'
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];
    
    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size] . ' ' . $class;
@endphp

<button type="{{ $type }}" 
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}>
    
    @if($loading)
        <i class="fas fa-spinner animate-spin -ml-1 mr-2 h-4 w-4"></i>
    @endif
    
    {{ $slot }}
</button>


