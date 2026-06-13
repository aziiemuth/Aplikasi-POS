<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi POS - Point of Sale modern untuk toko Anda">
    <title>@yield('title', 'Dashboard') — Aplikasi POS</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- FontAwesome 6 (Fase 0.2) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- SweetAlert2 (Fase 0.3) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Alpine.js (Fase 0.5) sudah dibundle oleh Livewire 3, jadi CDN dihapus untuk mencegah konflik --}}

    {{-- Vite for JS (Laravel Echo / Reverb) — dimuat setelah Tailwind config --}}

    {{-- Tailwind CSS via CDN (Fase 0.4) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        // Palet warna utama POS — elegan & soft
                        primary: {
                            50:  '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe',
                            300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6',
                            600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a',
                        },
                        surface: {
                            // Putih soft bergradasi — tidak menyilaukan mata kasir
                            DEFAULT: '#f8fafc',
                            50:  '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                        },
                        sidebar: '#1e293b',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn:  { '0%': { opacity: 0, transform: 'translateY(-8px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        slideIn: { '0%': { opacity: 0, transform: 'translateX(-16px)' }, '100%': { opacity: 1, transform: 'translateX(0)' } },
                    }
                }
            }
        }
    </script>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    @stack('styles')
</head>
<body class="font-sans bg-surface antialiased text-slate-800">

{{-- ===== LAYOUT WRAPPER ===== --}}
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR (Dynamic berdasarkan role) ===== --}}
    @include('layouts.partials.sidebar')

    {{-- ===== MAIN CONTENT AREA ===== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- ===== TOP NAVBAR ===== --}}
        @include('layouts.partials.navbar')

        {{-- ===== PAGE CONTENT ===== --}}
        <main class="flex-1 overflow-y-auto bg-surface p-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm animate-fade-in" id="flash-success">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span>{!! session('success') !!}</span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm animate-fade-in" id="flash-error">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                    <span>{!! session('error') !!}</span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>
</div>

{{-- ===== SweetAlert2 JS (Fase 0.3) ===== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ===== SweetAlert Global Config & Helpers ===== --}}
<script>
    // Konfigurasi default SweetAlert — proporsional & elegan
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        width: '360px',
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-slate-100',
        },
    });

    // Helper: konfirmasi hapus
    function confirmDelete(formId, itemName = 'item ini') {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?<br><small class="text-slate-500">Data masih bisa dipulihkan oleh Admin.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            width: '420px',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-5 py-2 text-sm font-semibold',
                cancelButton: 'rounded-xl px-5 py-2 text-sm font-semibold',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    // Auto-dismiss flash messages setelah 5 detik
    setTimeout(() => {
        document.getElementById('flash-success')?.remove();
        document.getElementById('flash-error')?.remove();
    }, 5000);
</script>

{{-- Livewire Scripts --}}
@livewireScripts

@stack('scripts')
</body>
</html>
