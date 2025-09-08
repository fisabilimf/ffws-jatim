@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'padding' => 'p-6',
    'class' => ''
])

<div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 {{ $class }}">
    @if($title || $subtitle || $actions)
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    @if($title)
                        <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
                @if($actions)
                    <div class="flex items-center space-x-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>


