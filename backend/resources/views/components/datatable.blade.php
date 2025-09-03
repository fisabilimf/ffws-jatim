@props([
    'title' => null,
    'headers' => [],
    'rows' => [],
    'searchable' => false,
    'searchPlaceholder' => 'Cari...',
    'paginationText' => null,
    'pagination' => null,
    'class' => ''
])

<div class="bg-white shadow rounded border border-gray-200 {{ $class }}">
    @if($title || $searchable || isset($filters) || isset($actions))
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center gap-3">
                    @if($title)
                        <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                    @endif
                    @isset($filters)
                        <div class="flex items-center gap-2">
                            {{ $filters }}
                        </div>
                    @endisset
                </div>
                <div class="flex items-center gap-2">
                    @if($searchable)
                        <label for="dt-search" class="sr-only">Cari</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-magnifying-glass h-5 w-5"></i>
                            </span>
                            <input id="dt-search" type="text" placeholder="{{ $searchPlaceholder }}" class="pl-10 pr-3 py-2 border rounded w-64 focus:outline-none focus:ring focus:border-blue-300" />
                        </div>
                    @endif
                    @isset($actions)
                        <div class="flex items-center gap-2">
                            {{ $actions }}
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    @endif

    <x-admin.table :headers="$headers" :rows="$rows" class="rounded-none border-0 shadow-none" />

    <div class="px-6 py-3 border-t border-gray-200 flex items-center justify-between">
        @if($paginationText)
            <p class="text-sm text-gray-600">{{ $paginationText }}</p>
        @else
            <span></span>
        @endif
        <div>
            {{ $pagination ?? '' }}
        </div>
    </div>
</div>


