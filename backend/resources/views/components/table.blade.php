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
                        <label for="search" class="sr-only">Cari</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-magnifying-glass h-5 w-5"></i>
                            </span>
                            <input id="search" type="text" placeholder="{{ $searchPlaceholder }}" 
                                   value="{{ $searchQuery }}"
                                   class="pl-10 pr-3 py-2 border rounded w-64 focus:outline-none focus:ring focus:border-blue-300" />
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
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50">
                        @foreach($headers as $header)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($header['format']))
                                    @switch($header['format'])
                                        @case('date')
                                            {{ \Carbon\Carbon::parse($row[$header['key']])->format('d/m/Y H:i') }}
                                            @break
                                        @case('status')
                                            @php
                                                $statusClasses = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'inactive' => 'bg-red-100 text-red-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'draft' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $statusClass = $statusClasses[$row[$header['key']] ?? 'draft'] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                                {{ ucfirst($row[$header['key']] ?? 'Unknown') }}
                                            </span>
                                            @break
                                        @case('actions')
                                            <div class="flex items-center space-x-2">
                                                @if(isset($row['actions']))
                                                    @foreach($row['actions'] as $action)
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
                                            {{ $row[$header['key']] ?? '' }}
                                    @endswitch
                                @else
                                    {{ $row[$header['key']] ?? '' }}
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
