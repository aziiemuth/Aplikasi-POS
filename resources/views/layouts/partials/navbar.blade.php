{{-- ===== TOP NAVBAR ===== --}}
<header class="bg-white border-b border-surface-200 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm z-10">

    {{-- Left Side: Hamburger & Title --}}
    <div class="flex items-center gap-3 min-w-0 flex-1">
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-lg shrink-0">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <div class="min-w-0 flex-1">
            <h2 class="text-sm sm:text-lg font-bold text-slate-800 leading-tight truncate">@yield('page-title', 'Dashboard')</h2>
            <p class="text-[10px] sm:text-xs text-slate-400 truncate mt-0.5">
                @yield('page-subtitle', 'Selamat datang, ' . (auth()->user()?->name ?? ''))
            </p>
        </div>
    </div>

    {{-- Right Side: Info & Actions --}}
    <div class="flex items-center gap-2 sm:gap-4 shrink-0 ml-3">

        {{-- Tanggal & Waktu Live --}}
        <div class="hidden md:flex items-center gap-2 text-sm text-slate-500 bg-surface-100 px-3 py-1.5 rounded-lg">
            <i class="fa-regular fa-clock text-primary-500"></i>
            <span id="live-clock">{{ now()->timezone('Asia/Jakarta')->format('H:i') }}</span>
            <span class="text-slate-400">•</span>
            <span id="live-date">{{ now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('ddd, D MMM Y') }}</span>
        </div>

        {{-- Notifikasi Stok Tipis (Admin Only) --}}
        @if(auth()->user()?->isAdmin())
            @php
                $lowStockCount = \App\Models\Product::active()->lowStock()->count();
            @endphp
            @if($lowStockCount > 0)
            <a href="{{ route('admin.products.index', ['stok' => 'tipis']) }}" 
                class="relative flex items-center justify-center sm:gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 w-9 h-9 sm:w-auto sm:px-3 sm:py-1.5 rounded-lg text-sm font-medium hover:bg-amber-100 transition-colors shrink-0 shadow-sm"
                title="{{ $lowStockCount }} stok tipis">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm sm:text-base"></i>
                <span class="hidden sm:inline">{{ $lowStockCount }} stok tipis</span>
                <span class="sm:hidden bg-amber-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center absolute -top-1.5 -right-1.5 border border-white shadow-sm">{{ $lowStockCount }}</span>
            </a>
            @endif
        @endif

        {{-- Avatar --}}
        <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md cursor-pointer shrink-0">
            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
        </div>
    </div>
</header>

<script>
    // Clock live update strict Asia/Jakarta
    function updateClock() {
        const now = new Date();
        
        const clock = document.getElementById('live-clock');
        if (clock) {
            clock.textContent = now.toLocaleTimeString('id-ID', { 
                timeZone: 'Asia/Jakarta', 
                hour: '2-digit', 
                minute: '2-digit' 
            }).replace('.', ':');
        }

        const dateEl = document.getElementById('live-date');
        if (dateEl) {
            // Menghasilkan format seperti: "Sab, 13 Jun 2026"
            dateEl.textContent = now.toLocaleDateString('id-ID', { 
                timeZone: 'Asia/Jakarta', 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            });
        }
    }
    
    setInterval(updateClock, 1000);
    // Langsung jalankan agar tidak menunggu 1 detik pertama
    updateClock();
</script>
