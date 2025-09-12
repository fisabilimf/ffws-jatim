   @props([
    'title' => null,
    'headers' => [],
    'rows' => [],
    'sortable' => false,
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'pagination' => null,
    'paginationText' => null,
    'searchable' => false,
    'searchQuery' => '',
    'searchPlaceholder' => 'Cari...',
    'class' => ''
])

@php
    // Auto detect if rows is a paginator instance
    $isPaginated = $rows instanceof \Illuminate\Pagination\LengthAwarePaginator || 
                   $rows instanceof \Illuminate\Pagination\Paginator ||
                   method_exists($rows, 'hasPages');
    
    // Extract data from paginator
    $tableRows = $isPaginated ? $rows->items() : $rows;
@endphp

<div class="bg-white shadow-sm rounded-lg border border-gray-200 {{ $class }}">
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
                        <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
                            @foreach(request()->except(['search', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <label for="search" class="sr-only">Cari</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fa-solid fa-magnifying-glass h-5 w-5"></i>
                                </span>
                                <input id="search" name="search" type="text" placeholder="{{ $searchPlaceholder }}" 
                                       value="{{ request('search', $searchQuery) }}"
                                       class="pl-10 pr-3 py-2 border rounded w-64 focus:outline-none focus:ring focus:border-blue-300" />
                            </div>
                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fa-solid fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ request()->url() }}" class="px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            @endif
                        </form>
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
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" 
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider
                                   {{ $sortable && isset($header['sortable']) && $header['sortable'] ? 'cursor-pointer hover:bg-gray-100' : '' }}"
                            @if($sortable && isset($header['sortable']) && $header['sortable'])
                                @click="window.location.href='{{ request()->fullUrlWithQuery(['sort' => $header['key'], 'direction' => $sortColumn === $header['key'] && $sortDirection === 'asc' ? 'desc' : 'asc']) }}'"
                            @endif>
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                @if($sortable && isset($header['sortable']) && $header['sortable'])
                                    @if($sortColumn === $header['key'])
                                        @if($sortDirection === 'asc')
                                            <i class="fas fa-sort-up w-4 h-4 text-gray-400"></i>
                                        @else
                                            <i class="fas fa-sort-down w-4 h-4 text-gray-400"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort w-4 h-4 text-gray-400"></i>
                                    @endif
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tableRows as $row)
                    <tr class="hover:bg-gray-50">
                        @foreach($headers as $header)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($header['format']))
                                    @switch($header['format'])
                                        @case('date')
                                            @if(isset($row->formatted_received_at))
                                                {{ \Carbon\Carbon::parse($row->formatted_received_at)->format('d/m/Y H:i') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($row[$header['key']])->format('d/m/Y H:i') }}
                                            @endif
                                            @break
                                        @case('sensor')
                                            <div class="text-sm font-medium text-gray-900">{{ $row->formatted_sensor ?? $row[$header['key']] }}</div>
                                            @if(isset($row->formatted_sensor_device))
                                                <div class="text-sm text-gray-500">{{ $row->formatted_sensor_device }}</div>
                                            @endif
                                            @break
                                        @case('parameter')
                                            <div class="text-sm text-gray-900">{{ $row->formatted_parameter ?? $row[$header['key']] }}</div>
                                            @if(isset($row->formatted_parameter_unit) && $row->formatted_parameter_unit)
                                                <div class="text-sm text-gray-500">{{ $row->formatted_parameter_unit }}</div>
                                            @endif
                                            @break
                                        @case('value')
                                            <span class="font-mono text-sm">{{ $row->formatted_value ?? $row[$header['key']] }}</span>
                                            @break
                                        @case('status')
                                            @php
                                                $statusValue = $row->formatted_threshold_status ?? $row[$header['key']];
                                                $statusClasses = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'inactive' => 'bg-red-100 text-red-800',
                                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'draft' => 'bg-gray-100 text-gray-800',
                                                    'safe' => 'bg-green-100 text-green-800',
                                                    'warning' => 'bg-yellow-100 text-yellow-800',
                                                    'danger' => 'bg-red-100 text-red-800'
                                                ];
                                                $statusClass = $statusClasses[$statusValue ?? 'draft'] ?? 'bg-gray-100 text-gray-800';
                                                
                                                // Custom labels for threshold status
                                                $statusLabels = [
                                                    'safe' => 'Aman',
                                                    'warning' => 'Waspada',
                                                    'danger' => 'Bahaya'
                                                ];
                                                $statusLabel = $statusLabels[$statusValue] ?? ucfirst($statusValue ?? 'Unknown');
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                            @break
                                        @case('actions')
                                            <div class="flex items-center space-x-2">
                                                @if(isset($row->formatted_actions))
                                                    @foreach($row->formatted_actions as $action)
                                                        @php
                                                            $rawType = strtolower(trim($action['type'] ?? ($action['label'] ?? '')));
                                                            $isDeleteByType = str_contains($rawType, 'hapus') || str_contains($rawType, 'delete') || str_contains($rawType, 'destroy');
                                                            $isViewByType = str_contains($rawType, 'detail') || str_contains($rawType, 'lihat') || str_contains($rawType, 'view') || str_contains($rawType, 'show');
                                                            $isEditByType = str_contains($rawType, 'edit');

                                                            $isDelete = isset($action['method']) && strtoupper($action['method']) === 'DELETE';
                                                            if (!$isDelete && $isDeleteByType) {
                                                                $isDelete = true;
                                                            }

                                                            $icon = $action['icon'] ?? null;
                                                            if (!$icon) {
                                                                $icon = $isDelete ? 'trash' : ($isEditByType ? 'pen' : ($isViewByType ? 'eye' : null));
                                                            }

                                                            $label = $action['label'] ?? null;
                                                            $color = $action['color'] ?? ($isDelete ? 'red' : 'gray');
                                                            $title = $action['title'] ?? ($label ?? ($isDelete ? 'Hapus' : ($isEditByType ? 'Edit' : ($isViewByType ? 'Detail' : 'Aksi'))));
                                                            $baseBtn = 'inline-flex items-center justify-center w-9 h-9 rounded-lg border text-sm transition';
                                                            $normalClasses = ' border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900';
                                                            $dangerClasses = ' border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700';
                                                            $btnClasses = $baseBtn . ($color === 'red' ? $dangerClasses : $normalClasses);
                                                        @endphp

                                                        @if($isDelete)
                                                            <form action="{{ $action['url'] }}" method="POST" class="inline" data-confirm-delete="{{ $action['confirm'] ?? 'Data yang dihapus tidak dapat dikembalikan. Lanjutkan?' }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="{{ $btnClasses }}" title="{{ $title }}">
                                                                    @if($icon)
                                                                        <i class="fas fa-{{ $icon }}"></i>
                                                                    @else
                                                                        <span class="sr-only">{{ $label ?? 'Hapus' }}</span>
                                                                        <i class="fas fa-trash"></i>
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        @elseif(isset($action['onclick']))
                                                            <button type="button" onclick="{{ $action['onclick'] }}" class="{{ $btnClasses }}" title="{{ $title }}">
                                                                @if($icon)
                                                                    <i class="fas fa-{{ $icon }}"></i>
                                                                @else
                                                                    <span class="sr-only">{{ $label ?? 'Aksi' }}</span>
                                                                    <i class="fas fa-ellipsis"></i>
                                                                @endif
                                                            </button>
                                                        @else
                                                            <a href="{{ $action['url'] }}" class="{{ $btnClasses }}" title="{{ $title }}">
                                                                @if($icon)
                                                                    <i class="fas fa-{{ $icon }}"></i>
                                                                @else
                                                                    <span class="sr-only">{{ $label ?? 'Detail' }}</span>
                                                                    <i class="fas fa-ellipsis"></i>
                                                                @endif
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            @break
                                        @default
                                            @if(isset($row->{'formatted_' . $header['key']}))
                                                {{ $row->{'formatted_' . $header['key']} }}
                                            @else
                                                {{ $row[$header['key']] ?? '' }}
                                            @endif
                                    @endswitch
                                @else
                                    @if(isset($row->{'formatted_' . $header['key']}))
                                        {{ $row->{'formatted_' . $header['key']} }}
                                    @else
                                        {{ $row[$header['key']] ?? '' }}
                                    @endif
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada data</p>
                                <p class="text-gray-500">Belum ada data yang tersedia.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Auto Pagination Section -->
    @if($isPaginated && $rows->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $rows->firstItem() }} sampai {{ $rows->lastItem() }} dari {{ $rows->total() }} data
                </div>
                <div class="flex items-center space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($rows->onFirstPage())
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $rows->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $currentPage = $rows->currentPage();
                        $lastPage = $rows->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    {{-- First page if not in range --}}
                    @if ($startPage > 1)
                        <a href="{{ $rows->url(1) }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">1</a>
                        @if ($startPage > 2)
                            <span class="px-2 text-gray-400">...</span>
                        @endif
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = $startPage; $i <= $endPage; $i++)
                        @if ($i == $currentPage)
                            <span class="px-3 py-2 text-sm text-white bg-blue-600 rounded-lg">{{ $i }}</span>
                        @else
                            <a href="{{ $rows->url($i) }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Last page if not in range --}}
                    @if ($endPage < $lastPage)
                        @if ($endPage < $lastPage - 1)
                            <span class="px-2 text-gray-400">...</span>
                        @endif
                        <a href="{{ $rows->url($lastPage) }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">{{ $lastPage }}</a>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($rows->hasMorePages())
                        <a href="{{ $rows->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @elseif($pagination || $paginationText)
        <!-- Manual Pagination Section -->
        <div class="px-6 py-3 border-t border-gray-200 flex items-center justify-between">
            @if($paginationText)
                <p class="text-sm text-gray-600">{{ $paginationText }}</p>
            @else
                <span></span>
            @endif
            <div>
                @if($pagination && is_string($pagination))
                    {!! $pagination !!}
                @else
                    {{ $pagination ?? '' }}
                @endif
            </div>
        </div>
    @endif
</div>
