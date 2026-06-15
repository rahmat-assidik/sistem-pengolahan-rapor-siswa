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
    
    {{-- NProgress --}}
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    
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
            letter-spacing: -0.03em;
        }

        @keyframes gentle-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .animate-gentle-pulse {
            animation: gentle-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        input, select, button { font-size: 0.875rem !important; }

        /* Custom NProgress Color */
        #nprogress .bar { background: #111827 !important; height: 3px !important; }
        #nprogress .spinner-icon { border-top-color: #111827 !important; border-left-color: #111827 !important; }
    </style>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // NProgress Global Setup
        NProgress.configure({ showSpinner: false, trickleSpeed: 200 });

        // Intercept form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('no-progress')) return;
            NProgress.start();
        });

        // Intercept link clicks
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && (link.classList.contains('no-progress') || link.target === '_blank')) return;
            
            if (link && link.getAttribute('href') && link.getAttribute('href') !== '#') {
                NProgress.start();
            }
        });

        // Hentikan NProgress jika kembali dari cache browser (tombol back)
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                NProgress.done();
            }
        });

        // Global Notification Helper
        window.showAlert = (message, type = 'success') => {
            Swal.fire({
                title: type === 'success' ? 'Berhasil' : (type === 'error' ? 'Kesalahan' : 'Informasi'),
                text: message,
                icon: type,
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl border border-gray-100 shadow-2xl',
                }
            });
        };

        window.addEventListener('notify', (e) => {
            if (e.detail && e.detail.message) {
                window.showAlert(e.detail.message, e.detail.type || 'success');
            }
        });
    </script>

    @stack('head-scripts')
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900" @yield('body-attrs')>

    {{-- Sidebar --}}
    <x-sidebar />
 
    {{-- Main Content --}}
    <main class="ml-0 lg:ml-52 min-h-screen bg-gray-50 p-4 lg:p-5 pt-16 lg:pt-4"> {{-- ml disesuaikan dengan lebar sidebar baru, responsive --}}
        @yield('content')
    </main>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof NProgress !== 'undefined') NProgress.done();

            @if(session('success'))
                window.showAlert(@js(session('success')), 'success');
            @endif

            @if(session('info'))
                window.showAlert(@js(session('info')), 'info');
            @endif

            @if(session('error'))
                window.showAlert(@js(session('error')), 'error');
            @endif
        });
    </script>
</body>
</html>
