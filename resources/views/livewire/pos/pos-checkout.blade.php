<div>
    @section('title', 'Point of Sale')
    @section('page-title', 'Point of Sale')
    @section('page-subtitle', 'Transaksi Cepat & Responsif')

    @push('styles')
    <style>
        .pos-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .pos-scroll::-webkit-scrollbar-track { background: transparent; }
        .pos-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .pos-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .product-card { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .product-card:active { transform: scale(0.97); }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Animasi item cart */
        @keyframes slideInRight { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }
        .cart-item-enter { animation: slideInRight 0.2s ease-out; }
    </style>
    @endpush

    {{-- Audio Beep --}}
    <audio id="beep-sound" src="{{ asset('scanner.mp3') }}" preload="auto"></audio>

    {{-- ===== LAYOUT UTAMA POS ===== --}}
    <div class="flex flex-col lg:flex-row gap-5 h-[calc(100vh-130px)] -mx-2 lg:mx-0">

        {{-- ===== KIRI: KATALOG PRODUK (70%) ===== --}}
        <div class="w-full lg:w-2/3 flex flex-col gap-4 h-full">

            {{-- Toolbar Atas: Search + Tombol Diagnostik --}}
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-surface-200 flex flex-col sm:flex-row gap-3 z-10">
                {{-- Search / Barcode Input --}}
                <div class="relative flex-1 flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama produk atau scan barcode/SKU..."
                            id="pos-search-input"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                            autofocus>
                    </div>
                    {{-- Tombol Scan Kamera --}}
                    <button type="button" onclick="openCameraScanner()"
                        class="px-4 py-2.5 bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 hover:border-blue-300 rounded-xl transition-colors font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-camera"></i>
                        <span class="hidden sm:inline">Scan Kamera</span>
                    </button>
                </div>

                {{-- Tombol Open Bill --}}
                <div class="flex gap-2">
                    @if(count($openBills) > 0)
                    <button wire:click="toggleOpenBills"
                        class="relative flex items-center gap-2 px-3 py-2.5 text-sm font-semibold rounded-xl border transition-all
                        {{ $showOpenBills ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
                        <i class="fa-solid fa-pause text-xs"></i>
                        Open Bill
                        <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ count($openBills) }}</span>
                    </button>
                    @endif
                </div>
            </div>

            {{-- ===== PANEL OPEN BILLS ===== --}}
            @if($showOpenBills && count($openBills) > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 animate-fade-in">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-amber-800 flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-pause-circle text-amber-600"></i>
                        Open Bills — Transaksi Tertunda
                    </h3>
                </div>
                <div class="space-y-2">
                    @foreach($openBills as $bill)
                    <div class="bg-white rounded-xl border border-amber-100 px-4 py-3 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">{{ $bill['nomor_order'] }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $bill['nama_customer'] }} ·
                                Rp {{ number_format($bill['total_pembayaran'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button wire:click="resumeOpenBill({{ $bill['id'] }})"
                                class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-play text-xs"></i> Lanjut
                            </button>
                            <button wire:click="cancelOpenBill({{ $bill['id'] }})"
                                class="px-3 py-1.5 text-xs font-semibold bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-xmark text-xs"></i> Batal
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Filter Kategori --}}
            <div class="flex gap-2 overflow-x-auto pos-scroll hide-scrollbar pb-1 shrink-0">
                <button wire:click="setCategory(null)"
                    class="whitespace-nowrap px-4 py-2 text-sm font-semibold rounded-xl transition-all
                    {{ is_null($categoryId) ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <i class="fa-solid fa-th-large mr-1"></i> Semua
                </button>
                @foreach($categories as $cat)
                <button wire:click="setCategory({{ $cat->id }})"
                    class="whitespace-nowrap px-4 py-2 text-sm font-semibold rounded-xl transition-all flex items-center gap-1.5
                    {{ $categoryId === $cat->id ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} text-xs"></i>
                    {{ $cat->nama_kategori }}
                </button>
                @endforeach
            </div>

            {{-- Grid Produk --}}
            <div class="flex-1 overflow-y-auto pos-scroll pb-4">
                @if($products->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-slate-400 space-y-3">
                    <i class="fa-solid fa-box-open text-5xl text-slate-200"></i>
                    <p>Tidak ada produk ditemukan</p>
                    @if($search)
                    <button wire:click="$set('search', '')" class="text-xs text-blue-600 hover:underline">Hapus pencarian</button>
                    @endif
                </div>
                @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3 lg:gap-4 px-1">
                    @foreach($products as $p)
                    @php $stokKosong = $p->stok_saat_ini <= 0; @endphp
                    <div wire:click="{{ $stokKosong ? '' : 'addToCart('.$p->id.')' }}"
                        class="product-card bg-white rounded-2xl border border-surface-200 overflow-hidden shadow-sm
                        {{ $stokKosong ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:shadow-md hover:-translate-y-1 group' }}">

                        <div class="aspect-square bg-slate-100 relative overflow-hidden">
                            @if($p->foto)
                                <img src="{{ Storage::url($p->foto) }}" class="w-full h-full object-contain {{ $stokKosong ? 'grayscale' : 'group-hover:scale-105 transition-transform duration-300' }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-image text-slate-300 text-4xl"></i>
                                </div>
                            @endif

                            {{-- Badge Stok --}}
                            <div class="absolute top-2 right-2 px-2 py-1 rounded-lg text-[10px] font-bold shadow-sm
                                {{ $stokKosong ? 'bg-rose-500 text-white' : ($p->stok_saat_ini <= $p->stok_minimum ? 'bg-amber-400 text-amber-900' : 'bg-white/90 text-slate-700') }}">
                                {{ $stokKosong ? 'Habis' : $p->stok_saat_ini . ' ' . $p->satuan }}
                            </div>
                        </div>

                        <div class="p-3">
                            <h3 class="text-xs font-semibold text-slate-800 line-clamp-2 leading-tight mb-1" title="{{ $p->nama_produk }}">
                                {{ $p->nama_produk }}
                            </h3>
                            <p class="text-blue-600 font-bold text-sm">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ===== KANAN: KERANJANG (30%) ===== --}}
        <div class="w-full lg:w-1/3 flex flex-col h-full bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">

            {{-- Header Keranjang --}}
            <div class="p-4 bg-slate-800 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-blue-400"></i>
                    <h2 class="font-bold">Keranjang</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-blue-600 text-xs px-2 py-1 rounded-md font-bold">{{ $carts->count() }} item</span>
                    <button wire:click="clearCart" wire:confirm="Kosongkan seluruh keranjang?" class="text-slate-400 hover:text-rose-400 transition-colors" title="Kosongkan keranjang">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>

            {{-- List Item Keranjang --}}
            <div class="flex-1 overflow-y-auto pos-scroll p-2 space-y-2 bg-slate-50/50">
                @if($carts->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-slate-400 space-y-3 opacity-60">
                    <i class="fa-solid fa-basket-shopping text-6xl text-slate-200"></i>
                    <p class="text-sm">Belum ada barang</p>
                </div>
                @else
                    @foreach($carts as $c)
                    <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm flex flex-col gap-2 relative group cart-item-enter"
                        x-data="{ editDiskon: false, diskonInput: {{ $c->diskon_item }} }">

                        {{-- Hapus Item --}}
                        <button wire:click="removeItem({{ $c->id }})" class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center rounded-full bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors opacity-0 group-hover:opacity-100">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>

                        <div class="pr-6">
                            <p class="text-sm font-semibold text-slate-800 leading-tight">{{ $c->product->nama_produk }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Rp {{ number_format($c->product->harga_jual, 0, ',', '.') }}
                                @if($c->diskon_item > 0)
                                <span class="text-rose-500 ml-1">- Rp {{ number_format($c->diskon_item, 0, ',', '.') }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            {{-- Qty Controls --}}
                            <div class="flex items-center bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                                <button wire:click="updateQuantity({{ $c->id }}, 'decrease')" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-200 hover:text-rose-600 transition-colors">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <span class="w-8 text-center text-xs font-bold text-slate-800">{{ $c->jumlah }}</span>
                                <button wire:click="updateQuantity({{ $c->id }}, 'increase')" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-200 hover:text-emerald-600 transition-colors">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>

                            {{-- Subtotal + Tombol Diskon Item --}}
                            <div class="flex flex-col items-end gap-0.5">
                                @php $totalItem = ($c->product->harga_jual - $c->diskon_item) * $c->jumlah; @endphp
                                <p class="font-bold text-slate-800 text-sm">Rp {{ number_format($totalItem, 0, ',', '.') }}</p>
                                {{-- Tombol edit diskon item (Fase 4.4) --}}
                                <button @click="editDiskon = !editDiskon" class="text-[10px] text-blue-500 hover:underline flex items-center gap-0.5">
                                    <i class="fa-solid fa-percent text-[8px]"></i>
                                    {{ $c->diskon_item > 0 ? 'Edit diskon' : 'Tambah diskon' }}
                                </button>
                            </div>
                        </div>

                        {{-- Input Diskon Item (Fase 4.4) --}}
                        <div x-show="editDiskon" x-transition class="flex gap-2 items-center pt-2 border-t border-slate-100">
                            <div class="relative flex-1">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">Rp</span>
                                <input type="number" x-model="diskonInput" min="0"
                                    placeholder="Diskon item..."
                                    class="w-full pl-7 pr-2 py-1.5 text-xs border border-slate-200 rounded-lg outline-none focus:ring-1 focus:ring-blue-200">
                            </div>
                            <button @click="$wire.updateDiskonItem({{ $c->id }}, diskonInput); editDiskon = false"
                                class="text-xs bg-blue-600 text-white px-2.5 py-1.5 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                OK
                            </button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- Ringkasan & Tombol Aksi --}}
            <div class="bg-white border-t border-slate-200 p-4 space-y-3 shrink-0">

                {{-- Diskon Global --}}
                <div x-data="{ editGlobal: false }">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-sm mt-1">
                        <button @click="editGlobal = !editGlobal" class="text-slate-500 hover:text-blue-600 flex items-center gap-1 transition-colors">
                            <i class="fa-solid fa-tag text-xs"></i>
                            Diskon Global
                        </button>
                        <span class="font-semibold text-rose-500">- Rp {{ number_format($diskonGlobal, 0, ',', '.') }}</span>
                    </div>

                    <div x-show="editGlobal" x-transition class="mt-1 flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">Rp</span>
                            <input type="number" wire:model.live="diskonGlobal" min="0" placeholder="0"
                                class="w-full pl-7 pr-2 py-1.5 text-xs border border-slate-200 rounded-lg outline-none focus:ring-1 focus:ring-blue-200">
                        </div>
                        <button @click="editGlobal = false" class="text-xs bg-slate-200 text-slate-700 px-2.5 py-1.5 rounded-lg hover:bg-slate-300">OK</button>
                    </div>
                </div>

                {{-- Total Akhir --}}
                <div class="pt-2 border-t border-slate-100 flex justify-between items-end">
                    <span class="text-slate-500 font-semibold mb-0.5">Total Akhir</span>
                    @php $totalAkhir = $subtotal - $diskonGlobal + $pajakPpn; @endphp
                    <span class="text-2xl font-bold text-blue-600">Rp {{ number_format(max(0, $totalAkhir), 0, ',', '.') }}</span>
                </div>

                {{-- Tombol Hold + Bayar --}}
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button wire:click="holdBill" wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-1.5 bg-amber-100 text-amber-700 hover:bg-amber-200 text-sm font-bold py-3 rounded-xl transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="holdBill"><i class="fa-solid fa-pause mr-1"></i> Hold Bill</span>
                        <span wire:loading wire:target="holdBill"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                    </button>
                    <button wire:click="openPaymentModal" wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-1.5 bg-blue-600 text-white hover:bg-blue-700 text-sm font-bold py-3 rounded-xl shadow-md hover:-translate-y-0.5 transition-all disabled:opacity-50">
                        <span wire:loading.remove wire:target="openPaymentModal"><i class="fa-solid fa-money-bill-wave mr-1"></i> Bayar</span>
                        <span wire:loading wire:target="openPaymentModal"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                    </button>
                </div>
            </div>
        </div>

    </div>


    {{-- ===== MODAL PEMBAYARAN ===== --}}
    <div x-data="{ open: @entangle('showPaymentModal') }"
         x-show="open"
         style="display: none"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modal-title">
                        <i class="fa-solid fa-cash-register text-blue-400"></i> Proses Pembayaran
                    </h3>
                    <button @click="open = false" class="text-slate-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    {{-- Total Tagihan --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                        <p class="text-sm text-blue-600 font-semibold mb-1">Total Tagihan</p>
                        @php $totalAkhir = $subtotal - $diskonGlobal + $pajakPpn; @endphp
                        <p class="text-3xl font-bold text-blue-800">Rp {{ number_format(max(0, $totalAkhir), 0, ',', '.') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Customer</label>
                            <input type="text" wire:model="namaCustomer" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Metode Bayar</label>
                            <select wire:model="metodePembayaran" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                                <option value="Tunai">💵 Tunai</option>
                                <option value="Debit BCA">💳 Debit BCA</option>
                                <option value="OVO">📱 OVO</option>
                                <option value="DANA">📱 DANA</option>
                                <option value="GoPay">📱 GoPay</option>
                                <option value="QRIS">📲 QRIS</option>
                                <option value="Transfer">🏦 Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Uang Diterima <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                            <input type="number" wire:model.live="jumlahBayar"
                                class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-4 py-3 text-lg font-bold text-slate-800 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all">
                        </div>

                        {{-- Kalkulator Kembalian Otomatis --}}
                        @php
                            $bayar = (float) $jumlahBayar;
                            $tagihan = max(0, $totalAkhir ?? 0);
                            $kembali = $bayar - $tagihan;
                        @endphp
                        @if($bayar > 0)
                            @if($kembali < 0)
                                <p class="text-xs text-rose-500 mt-2 font-semibold bg-rose-50 rounded-lg px-3 py-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Uang kurang Rp {{ number_format(abs($kembali), 0, ',', '.') }}
                                </p>
                            @else
                                <p class="text-xs text-emerald-600 mt-2 font-semibold bg-emerald-50 rounded-lg px-3 py-2">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Kembalian: <strong>Rp {{ number_format($kembali, 0, ',', '.') }}</strong>
                                </p>
                            @endif
                        @endif

                        {{-- Tombol nominal cepat --}}
                        <div class="flex gap-2 mt-2 flex-wrap">
                            @php
                                $tagihan2 = max(0, $totalAkhir ?? 0);
                                $nominals = [
                                    (int) ceil($tagihan2 / 10000) * 10000,
                                    50000, 100000,
                                ];
                                $nominals = array_unique(array_filter($nominals, fn($n) => $n >= $tagihan2));
                                sort($nominals);
                            @endphp
                            @foreach(array_slice($nominals, 0, 3) as $nom)
                            <button type="button" wire:click="$set('jumlahBayar', {{ $nom }})"
                                class="text-[11px] px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors font-semibold">
                                Rp {{ number_format($nom, 0, ',', '.') }}
                            </button>
                            @endforeach
                            <button type="button" wire:click="$set('jumlahBayar', {{ max(0, $totalAkhir ?? 0) }})"
                                class="text-[11px] px-2.5 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-semibold">
                                Uang Pas
                            </button>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catatan (opsional)</label>
                        <input type="text" wire:model="catatan" placeholder="Catatan transaksi..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button @click="open = false" type="button" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="processCheckout" wire:loading.attr="disabled" type="button"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md transition-all disabled:opacity-50 flex items-center gap-2">
                        <span wire:loading.remove wire:target="processCheckout"><i class="fa-solid fa-check"></i> Proses Transaksi</span>
                        <span wire:loading wire:target="processCheckout"><i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CAMERA SCANNER --}}
    <div id="camera-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden animate-fade-in flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-camera text-blue-600"></i> Scanner Kamera
                </h3>
                <button onclick="closeCameraScanner()" class="text-slate-400 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-6 relative">
                <div id="reader" class="w-full h-auto min-h-[300px] bg-slate-900 rounded-xl overflow-hidden"></div>
                <div id="scan-feedback" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-emerald-500 text-white px-4 py-2 rounded-lg font-bold shadow-xl hidden flex-col items-center gap-1 z-10">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                    <span>Terpindai!</span>
                </div>
            </div>
        </div>
    </div>

    {{-- FASE 6: MODAL STRUK TRANSAKSI --}}
    @if($showReceiptModal && $receiptOrderId)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4 animate-fade-in">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-blue-600"></i> Struk Transaksi
                </h3>
                <button wire:click="closeReceiptModal" class="text-slate-400 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 bg-slate-100 overflow-y-auto flex-1 flex justify-center">
                {{-- Area Preview Struk --}}
                <div class="bg-white shadow-sm border border-slate-200 flex justify-center py-2" style="width: 64mm; min-height: 200px; overflow: hidden;" id="receipt-preview-container">
                    <iframe src="{{ route('kasir.struk', $receiptOrderId) }}" id="receipt-iframe" class="border-none pointer-events-none" style="width: 58mm; min-height: 400px;"></iframe>
                </div>
            </div>

            <div class="bg-white px-6 py-4 border-t border-slate-100">
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="printUSB()" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
                        <i class="fa-solid fa-print"></i> Cetak USB
                    </button>
                    <button type="button" onclick="printBluetooth('{{ $receiptOrderId }}')" class="flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors">
                        <i class="fa-brands fa-bluetooth-b"></i> Bluetooth
                    </button>
                </div>
                <button wire:click="closeReceiptModal" class="mt-3 w-full py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Tutup & Layani Pelanggan Berikutnya
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    // ========================================
    // BEEP SOUND SAAT BARANG MASUK KERANJANG
    // ========================================
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('play-beep', () => {
            const audio = document.getElementById('beep-sound');
            if (audio) { audio.currentTime = 0; audio.play().catch(() => {}); }
        });

        // SweetAlert Toast untuk notifikasi Livewire
        Livewire.on('swal', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                title: data.title,
                text: data.text,
                icon: data.icon,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: data.icon === 'error' ? 5000 : 3000,
                timerProgressBar: true,
                width: '380px',
                customClass: { popup: 'rounded-2xl shadow-xl border border-slate-100 text-sm' }
            });
        });

        // Print struk setelah transaksi berhasil
        Livewire.on('print-struk', (event) => {
            console.log('Struk siap cetak untuk Order ID:', event.orderId);
            // TODO Fase 6: Implementasi print struk ke thermal printer
        });
    });

    // ========================================
    // GLOBAL BARCODE SCANNER (Fase 5.1)
    // Standby tanpa perlu klik kolom dulu
    // ========================================
    let scanBuffer = '';
    let scanTimer  = null;
    const SCAN_DELAY = 100; // ms antara karakter — scanner fisik biasanya < 100ms/karakter

    document.addEventListener('keypress', function(e) {
        // Abaikan jika sedang di input field lain (modal, dll)
        const target = e.target.tagName.toLowerCase();
        if (['input', 'textarea', 'select'].includes(target) && e.target.id !== 'pos-search-input') return;

        if (e.key === 'Enter') {
            if (scanBuffer.length > 0) {
                // Trigger pencarian SKU & Add to Cart
                @this.call('handleBarcodeScan', scanBuffer);
                scanBuffer = '';
                clearTimeout(scanTimer);
            }
        } else {
            scanBuffer += e.key;
            clearTimeout(scanTimer);
            scanTimer = setTimeout(() => { scanBuffer = ''; }, 500);
        }
    });

    // ========================================
    // CAMERA SCANNER (Fase 5.2)
    // ========================================
    let html5QrcodeScanner = null;
    let scanDelay = false;

    function openCameraScanner() {
        document.getElementById('camera-modal').classList.remove('hidden');
        document.getElementById('camera-modal').classList.add('flex');
        
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
                fps: 10, 
                qrbox: {width: 250, height: 150},
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            }, false);
            
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }
    }

    function closeCameraScanner() {
        document.getElementById('camera-modal').classList.add('hidden');
        document.getElementById('camera-modal').classList.remove('flex');
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(error => console.error("Failed to clear scanner. ", error));
            html5QrcodeScanner = null;
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (scanDelay) return; // Mencegah scan berulang
        scanDelay = true;
        
        // UI Feedback
        const feedback = document.getElementById('scan-feedback');
        feedback.classList.remove('hidden');
        feedback.classList.add('flex');
        
        // Trigger Livewire handleBarcodeScan
        @this.call('handleBarcodeScan', decodedText);
        
        // 3 detik delay sebelum bisa scan lagi
        setTimeout(() => {
            scanDelay = false;
            feedback.classList.remove('flex');
            feedback.classList.add('hidden');
        }, 3000);
    }

    function onScanFailure(error) {
        // Abaikan error saat tidak ada barcode yang terdeteksi
    }

    // ========================================
    // FASE 6: PENCETAKAN STRUK (JS)
    // ========================================
    
    function printUSB() {
        const iframe = document.getElementById('receipt-iframe');
        if (iframe) {
            iframe.contentWindow.print();
        }
    }

    async function printBluetooth(orderId) {
        try {
            // Meminta device bluetooth (hanya yang mendukung Serial Port Profile / printer)
            const device = await navigator.bluetooth.requestDevice({
                filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }],
                optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb'] // UUID generik untuk thermal printer bluetooth
            });
            
            const server = await device.gatt.connect();
            const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
            const characteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

            // Ambil teks murni dari iframe
            const iframe = document.getElementById('receipt-iframe');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            let textToPrint = iframeDoc.body.innerText || "Struk Transaksi POS\n\nTerima Kasih!";
            
            // Tambahkan baris kosong dan perintah pemotong (jika ada) di akhir
            textToPrint += "\n\n\n";

            // Encode string ke array buffer
            const encoder = new TextEncoder();
            const data = encoder.encode(textToPrint);

            // Karena limit Bluetooth LE biasanya 20-512 byte per paket, kita pecah-pecah jika terlalu besar
            const CHUNK_SIZE = 100;
            for (let i = 0; i < data.length; i += CHUNK_SIZE) {
                const chunk = data.slice(i, i + CHUNK_SIZE);
                await characteristic.writeValue(chunk);
            }

            Swal.fire('Berhasil!', 'Struk sedang dicetak via Bluetooth.', 'success');
        } catch (error) {
            console.error('Bluetooth Print Error:', error);
            Swal.fire('Error Bluetooth', 'Gagal koneksi ke printer Bluetooth. Pastikan bluetooth menyala dan browser mendukung Web Bluetooth API.', 'error');
        }
    }

    </script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    @endpush
</div>
