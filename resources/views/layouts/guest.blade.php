<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Rapor')</title>

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
                    }
                }
            }
        }
    </script>
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            letter-spacing: -0.03em;
        }
    </style>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-white font-sans px-4 antialiased">

    @yield('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: { message: "{{ session('success') }}", type: 'success' }
                }));
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
