@props([
    'headers' => [],
    'rows' => [],
    'sortable' => false,
    'sortColumn' => null,
    'sortDirection' => 'asc',
    'pagination' => null,
    'searchable' => false,
    'searchQuery' => '',
    'class' => ''
])

<div class="bg-white shadow-sm rounded-lg border border-gray-200 {{ $class }}">
    @if($searchable)
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="search" class="sr-only">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ $searchQuery }}"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Cari...">
                    </div>
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
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortColumn === $header['key'])
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                        @endif
                                    </svg>
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
                                                        <a href="{{ $action['url'] }}" 
                                                           class="text-{{ $action['color'] ?? 'blue' }}-600 hover:text-{{ $action['color'] ?? 'blue' }}-900">
                                                            {{ $action['label'] }}
                                                        </a>
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
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada data</p>
                                <p class="text-gray-500">Belum ada data yang tersedia.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pagination)
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $pagination }}
        </div>
    @endif
</div>
