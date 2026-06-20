{{-- resources/views/components/modal.blade.php --}}
@props(['name' => 'openTambah', 'title' => 'Modal', 'maxWidth' => 'max-w-lg'])

<div
    x-show="{{ $name }}"
    x-cloak
    x-init="$watch('{{ $name }}', value => {
        if (value) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    })"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto"
>
    {{-- Backdrop --}}
    <div 
        x-show="{{ $name }}"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/40"
    ></div>

    {{-- Modal Content --}}
    <div
        x-show="{{ $name }}"
        x-transition:enter="transition duration-400"
        x-transition:enter-start="opacity-0 -translate-y-12 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
        style="transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);"
        class="relative bg-white rounded shadow-[0_20px_60px_rgba(0,0,0,0.25)] overflow-y-auto max-h-[90vh] w-full {{ $maxWidth }} transform transition-all z-10"
    >
        {{-- Modal Header --}}
        <div class="px-6 pt-6 pb-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-gray-50 text-gray-900 flex items-center justify-center shadow-sm border border-gray-100">
                        <i class="fa-solid fa-folder-open text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 tracking-tight">{{ $title }}</h2>
                        <p class="text-[11px] text-gray-500 font-medium">Manajemen Basis Data Terpusat</p>
                    </div>
                </div>
                <button
                    @click="{{ $name }} = false"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors outline-none border-none focus:outline-none"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-4">
            <div class="h-px bg-gray-100 w-full mb-5"></div>
            {{ $slot }}
        </div>
    </div>
</div>



