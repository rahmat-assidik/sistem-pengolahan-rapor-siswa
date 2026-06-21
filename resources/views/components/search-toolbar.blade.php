{{-- resources/views/components/search-toolbar.blade.php --}}
@props([
    'placeholder' => 'Cari...',
    'showTambah' => true,
    'tambahClick' => 'openTambah = true',
    'importClick' => null,
    'importLabel' => 'Import Excel',
    'resetUrl' => null,
    'filters' => [] {{-- Array of objects: ['name' => 'status', 'label' => 'Status', 'options' => ['Aktif', 'Tidak Aktif']] --}}
])

<div class="p-6 border-b border-gray-200">
    <form action="" method="GET" class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        {{-- Left: Search Input --}}
        <div class="w-full md:w-auto md:max-w-xs flex-1">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}"
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded focus:outline-none focus:border-gray-900 transition-colors bg-white">
            </div>
        </div>

        {{-- Right: Filter & Tombol Tambah --}}
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            @foreach($filters as $filter)
            <select name="{{ $filter['name'] }}" 
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded focus:outline-none focus:border-gray-900 transition-colors bg-white cursor-pointer min-w-[140px]">
                <option value="">{{ $filter['label'] }}</option>
                @foreach($filter['options'] as $key => $label)
                    <option value="{{ $key }}" {{ request($filter['name']) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @endforeach

            <button
                type="submit"
                class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Cari</span>
            </button>

            @if($resetUrl && request()->anyFilled(array_merge(['search'], array_column($filters, 'name'))))
                <a href="{{ $resetUrl }}" class="px-4 py-2.5 text-gray-500 hover:text-red-600 transition-colors text-xs font-semibold tracking-tight whitespace-nowrap">
                    Reset
                </a>
            @endif

            @if($importClick)
            <button
                type="button"
                @click="{{ $importClick }}"
                class="px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded hover:bg-green-700 transition-colors flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-file-excel"></i>
                <span>{{ $importLabel }}</span>
            </button>
            @endif

            @if($showTambah)
            <button
                type="button"
                @click="{{ $tambahClick }}"
                class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-circle-plus"></i>
                <span>Tambah</span>
            </button>
            @endif
        </div>
    </form>
</div>
