{{-- ===== SIDEBAR — Dynamic berdasarkan Role (Fase 2.1 + 3.1) ===== --}}
{{-- Mobile Overlay --}}
<div x-cloak x-show="sidebarOpen" x-transition.opacity
     @click="sidebarOpen = false"
     style="display: none;"
     class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-20 md:hidden"></div>

<aside id="sidebar"
    class="w-64 bg-sidebar flex flex-col shadow-2xl transition-all duration-300 ease-in-out z-30 fixed inset-y-0 left-0 md:relative -translate-x-full md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    {{-- Logo / App Name --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
        <div class="w-9 h-9 bg-primary-500 rounded-xl flex items-center justify-center shrink-0 shadow-lg">
            <i class="fa-solid fa-cash-register text-white text-base"></i>
        </div>
        <div>
            <h1 class="text-white font-bold text-base leading-tight">Aplikasi POS</h1>
            <p class="text-slate-400 text-xs">Point of Sale</p>
        </div>
    </div>


    {{-- Navigation Menu --}}
    <nav id="sidebar-scroll" class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 scrollbar-thin scrollbar-track-slate-800 scrollbar-thumb-slate-600">

        {{-- ===== MENU KASIR (Semua role) ===== --}}
        <div class="mb-3 flex flex-col gap-1">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 mb-1.5">Transaksi</p>

            <a href="{{ route('kasir.pos') }}"
               class="sidebar-link {{ request()->routeIs('kasir.pos') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register w-5 text-center text-blue-400"></i>
                <span>Point of Sale</span>
            </a>

            <a href="{{ route('kasir.riwayat') }}"
               class="sidebar-link {{ request()->routeIs('kasir.riwayat') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center text-indigo-400"></i>
                <span>Riwayat Transaksi</span>
            </a>

            @if(!auth()->user()->isAdmin())
            <a href="{{ route('kasir.tools.diagnostik') }}"
               class="sidebar-link {{ request()->routeIs('kasir.tools.diagnostik') ? 'active' : '' }}">
                <i class="fa-solid fa-stethoscope w-5 text-center text-teal-400"></i>
                <span>Uji Alat Kasir</span>
            </a>
            @endif
        </div>

        {{-- ===== MENU ADMIN ONLY ===== --}}
        @if(auth()->user()->isAdmin())

        {{-- Dashboard --}}
        <div class="mb-3 pt-2 border-t border-slate-700/60 flex flex-col gap-1">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 mb-1.5">Admin Panel</p>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center text-cyan-400"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users w-5 text-center text-sky-400"></i>
                <span>Manajemen User</span>
            </a>
        </div>

        {{-- === FASE 3: Master Data === --}}
        <div class="mb-3 pt-2 border-t border-slate-700/60 flex flex-col gap-1">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 mb-1.5">Master Data</p>

            <a href="{{ route('admin.categories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags w-5 text-center text-amber-400"></i>
                <span>Kategori</span>
            </a>

            <a href="{{ route('admin.suppliers.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck w-5 text-center text-orange-400"></i>
                <span>Supplier</span>
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked w-5 text-center text-violet-400"></i>
                <span>Produk</span>
                @php $lowStock = \App\Models\Product::active()->lowStock()->count(); @endphp
                @if($lowStock > 0)
                <span class="ml-auto text-xs bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded-md font-medium">
                    {{ $lowStock }} tipis
                </span>
                @endif
            </a>
        </div>

        {{-- === FASE 3: Manajemen Stok === --}}
        <div class="mb-3 pt-2 border-t border-slate-700/60 flex flex-col gap-1">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 mb-1.5">Manajemen Stok</p>

            <a href="{{ route('admin.stock.masuk') }}"
               class="sidebar-link {{ request()->routeIs('admin.stock.masuk') ? 'active' : '' }}">
                <i class="fa-solid fa-download w-5 text-center text-emerald-400"></i>
                <span>Stok Masuk</span>
            </a>

            <a href="{{ route('admin.stock.keluar') }}"
               class="sidebar-link {{ request()->routeIs('admin.stock.keluar') ? 'active' : '' }}">
                <i class="fa-solid fa-upload w-5 text-center text-rose-400"></i>
                <span>Stok Keluar</span>
            </a>

            <a href="{{ route('admin.stock.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.stock.index') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-list w-5 text-center text-fuchsia-400"></i>
                <span>Riwayat Mutasi</span>
            </a>
        </div>

        {{-- === FASE 7: Laporan Bisnis === --}}
        <div class="mb-3 pt-2 border-t border-slate-700/60 flex flex-col gap-1">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 mb-1.5">Laporan</p>

            <a href="{{ route('admin.laporan.penjualan') }}"
               class="sidebar-link {{ request()->routeIs('admin.laporan.penjualan*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line w-5 text-center text-emerald-400"></i>
                <span>Laporan Penjualan</span>
            </a>

            <a href="{{ route('admin.laporan.stok') }}"
               class="sidebar-link {{ request()->routeIs('admin.laporan.stok*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-list w-5 text-center text-cyan-400"></i>
                <span>Laporan Stok</span>
            </a>

            <a href="{{ route('admin.laporan.activity-log') }}"
               class="sidebar-link {{ request()->routeIs('admin.laporan.activity-log') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved w-5 text-center text-slate-400"></i>
                <span>Log Aktivitas</span>
            </a>
        </div>


        {{-- === FASE 8: Pengaturan Sistem === --}}
        <div class="mb-3 pt-2 border-t border-slate-700/60 flex flex-col gap-1">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-3 mb-1.5">Sistem</p>

            <a href="{{ route('admin.pengaturan.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.pengaturan.index') ? 'active' : '' }}">
                <i class="fa-solid fa-gear w-5 text-center text-slate-400"></i>
                <span>Pengaturan</span>
            </a>

            <a href="{{ route('admin.pengaturan.guide') }}"
               class="sidebar-link {{ request()->routeIs('admin.pengaturan.guide') ? 'active' : '' }}">
                <i class="fa-solid fa-book-open w-5 text-center text-amber-400"></i>
                <span>Petunjuk Penggunaan</span>
            </a>

        </div>

        @endif

    </nav>

    <div class="mt-auto border-t border-slate-700 bg-slate-800/40">
        {{-- User Info --}}
        <div class="flex items-center gap-3 px-6 py-4">
            <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center shrink-0 text-white font-bold text-sm shadow">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-white text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full mt-0.5
                    {{ auth()->user()->isAdmin() ? 'bg-amber-500/20 text-amber-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                    <i class="fa-solid {{ auth()->user()->isAdmin() ? 'fa-shield-halved' : 'fa-user-tie' }} text-xs"></i>
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>

        {{-- Logout Button --}}
        <div class="px-4 pb-4">
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="button"
                    onclick="confirmLogout()"
                    class="w-full flex items-center gap-3 px-4 py-2 text-slate-300 hover:text-white hover:bg-red-600/20 rounded-xl transition-all duration-200 text-sm font-medium group">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center group-hover:text-red-400 transition-colors"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

@push('scripts')
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin keluar dari sistem?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-right-from-bracket mr-1"></i> Ya, Logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        width: '380px',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
    }).then((r) => { if (r.isConfirmed) document.getElementById('logout-form').submit(); });
}

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar-scroll');
    if (sidebar) {
        // Kembalikan posisi scroll terakhir
        const scrollPos = localStorage.getItem('sidebar_scroll_pos');
        if (scrollPos) {
            sidebar.scrollTop = scrollPos;
        }

        // Simpan posisi scroll setiap kali berubah
        sidebar.addEventListener('scroll', function() {
            localStorage.setItem('sidebar_scroll_pos', sidebar.scrollTop);
        });
    }
});
</script>
@endpush
