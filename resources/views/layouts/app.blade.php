<!DOCTYPE html>
<html lang="en" class="text-[14px]"> {{-- Mengecilkan basis ukuran font --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Sistem Pengolahan Rapor</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    
    {{-- CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Inter', sans-serif; 
            letter-spacing: -0.03em; /* Slightly more aggressive tightening for Inter */
        }
        {{-- Mengoptimalkan tampilan pada zoom 100% agar lebih padat --}}
        input, select, button { font-size: 0.875rem !important; }
    </style>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        window.addEventListener('notify', (e) => {
            const type = e.detail.type || 'success';
            const message = e.detail.message;
            
            Swal.fire({
                title: type === 'success' ? 'Berhasil' : (type === 'error' ? 'Kesalahan' : 'Informasi'),
                text: message,
                icon: type,
                confirmButtonText: 'Selesai',
                confirmButtonColor: '#111827',
                customClass: {
                    popup: 'rounded-xl border border-gray-100 shadow-2xl',
                    confirmButton: 'px-6 py-2.5 text-xs font-bold uppercase tracking-widest rounded transition-all active:scale-[0.98]'
                }
            });
        });
    </script>

    @stack('head-scripts')
    @stack('styles')
<body class="bg-gray-50 font-sans antialiased text-gray-900" @yield('body-attrs')>
 


    {{-- Sidebar --}}
    <x-sidebar />
 
    {{-- Main Content --}}
    <main class="ml-0 lg:ml-52 min-h-screen bg-gray-50 p-4 lg:p-5 pt-16 lg:pt-4"> {{-- ml disesuaikan dengan lebar sidebar baru, responsive --}}
        <div class="flex flex-col gap-5">
            {{-- Error Alerts di dalam main agar tetap nempel di atas konten --}}
            <div class="space-y-3">
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition
                         class="p-4 bg-red-50 border border-red-200 rounded text-red-700 text-xs font-bold flex items-center justify-between shadow-sm transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded bg-red-500 text-white flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                            </div>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition
                         class="p-4 bg-red-50 border border-red-200 rounded text-red-700 text-xs font-bold space-y-2 shadow-sm border-l-4 border-l-red-500 transition-all relative">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span class="uppercase tracking-wider">Terjadi Kesalahan:</span>
                        </div>
                        <ul class="list-disc list-inside pl-1 space-y-1 font-medium text-[11px]">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button @click="show = false" class="absolute top-4 right-4 text-red-400 hover:text-red-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @yield('content')
    </div>
</main>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: "{{ session('success') }}", type: 'success' }
                }));
            @endif
        });
    </script>
</body>
</html>
