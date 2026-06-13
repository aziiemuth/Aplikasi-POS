<div>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            Riwayat Transaksi
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 animate-fade-in">

        {{-- Filter Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4"><i class="fa-solid fa-filter mr-2"></i>Filter Pencarian</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal</label>
                    <select wire:model.live="tanggal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="">Semua Tanggal</option>
                        @for($i=1; $i<=31; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Bulan</label>
                    <select wire:model.live="bulan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="">Semua Bulan</option>
                        @php
                            $bulans = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($bulans as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tahun</label>
                    <select wire:model.live="tahun" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="">Semua Tahun</option>
                        @for($y=date('Y'); $y>=date('Y')-5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="$set('tanggal', ''); $set('bulan', ''); $set('tahun', '');" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tgl & Waktu</th>
                            <th class="px-6 py-4 font-semibold">No. Order</th>
                            <th class="px-6 py-4 font-semibold">Kasir</th>
                            <th class="px-6 py-4 font-semibold text-right">Total Transaksi</th>
                            <th class="px-6 py-4 font-semibold text-center">Metode</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800">
                                {{ $order->nomor_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                {{ $order->user->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800 text-right">
                                Rp {{ number_format($order->total_pembayaran, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-blue-50 text-blue-600">
                                    {{ $order->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="viewDetail({{ $order->id }})" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors flex items-center justify-center mx-auto" title="Lihat Detail & Cetak Struk">
                                    <i class="fa-solid fa-receipt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-file-invoice text-2xl text-slate-400"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold mb-1">Tidak Ada Transaksi</h3>
                                <p class="text-slate-500 text-sm">Belum ada riwayat transaksi pada filter yang dipilih.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $orders->links(data: ['scrollTo' => false]) }}
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL DETAIL & CETAK STRUK --}}
    @if($showDetailModal && $selectedOrder)
    <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4 animate-fade-in">
        <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] border border-slate-100">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg leading-tight">Detail Transaksi</h3>
                        <p class="text-xs text-slate-500 font-medium">Order #{{ $selectedOrder->nomor_order }}</p>
                    </div>
                </div>
                <button wire:click="closeDetail" class="w-10 h-10 rounded-full bg-slate-50 hover:bg-red-50 text-slate-400 hover:text-red-500 transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-6 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 bg-slate-50/50">
                
                {{-- Kiri: Detail Informasi --}}
                <div class="lg:col-span-2 space-y-5">
                    {{-- Info Card --}}
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-2 gap-y-4 gap-x-6">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Waktu Transaksi</p>
                            <p class="font-semibold text-slate-800">{{ $selectedOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Pelanggan</p>
                            <p class="font-semibold text-slate-800">{{ $selectedOrder->nama_customer }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Kasir</p>
                            <p class="font-semibold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-circle-user text-slate-300"></i> {{ $selectedOrder->user->name ?? 'Kasir' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Metode</p>
                            <span class="inline-flex px-2 py-0.5 text-[11px] font-bold rounded-md bg-blue-50 text-blue-600">
                                {{ $selectedOrder->metode_pembayaran }}
                            </span>
                        </div>
                    </div>

                    {{-- Tabel Barang --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                            <h4 class="text-sm font-bold text-slate-700">Daftar Belanja</h4>
                        </div>
                        <table class="w-full text-sm">
                            <thead class="bg-white text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-5 text-left font-medium text-xs uppercase tracking-wider">Item</th>
                                    <th class="py-3 px-5 text-center font-medium text-xs uppercase tracking-wider">Qty</th>
                                    <th class="py-3 px-5 text-right font-medium text-xs uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($selectedOrder->items as $item)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 px-5">
                                        <p class="font-bold text-slate-800">{{ $item->nama_produk_snapshot }}</p>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs">
                                            <span class="text-slate-500">Rp {{ number_format($item->harga_jual_snapshot, 0, ',', '.') }}</span>
                                            @if($item->diskon_item > 0)
                                                <span class="bg-rose-50 text-rose-600 px-1.5 py-0.5 rounded font-medium border border-rose-100">-Rp {{ number_format($item->diskon_item, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-5 text-center font-semibold text-slate-600">x{{ $item->jumlah }}</td>
                                    <td class="py-3 px-5 text-right font-bold text-slate-800">Rp {{ number_format($item->total_harga_item, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        {{-- Ringkasan Pembayaran --}}
                        <div class="p-5 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                            <div class="w-full sm:w-2/3 lg:w-1/2 space-y-2 text-sm">
                                <div class="flex justify-between text-slate-500 font-medium">
                                    <span>Subtotal</span>
                                    <span>Rp {{ number_format($selectedOrder->total_sebelum_diskon, 0, ',', '.') }}</span>
                                </div>
                                @if($selectedOrder->diskon_global > 0)
                                <div class="flex justify-between text-rose-500 font-medium">
                                    <span>Diskon Transaksi</span>
                                    <span>-Rp {{ number_format($selectedOrder->diskon_global, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                @if($selectedOrder->pajak_ppn > 0)
                                <div class="flex justify-between text-slate-500 font-medium">
                                    <span>PPN</span>
                                    <span>Rp {{ number_format($selectedOrder->pajak_ppn, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between font-black text-slate-800 text-lg pt-3 border-t border-slate-200 mt-2">
                                    <span>TOTAL</span>
                                    <span>Rp {{ number_format($selectedOrder->total_pembayaran, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-slate-500 font-medium pt-2">
                                    <span>Tunai/Bayar</span>
                                    <span>Rp {{ number_format($selectedOrder->jumlah_bayar, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg mt-1 border border-emerald-100">
                                    <span>Kembalian</span>
                                    <span>Rp {{ number_format($selectedOrder->kembalian, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Preview Struk & Aksi --}}
                <div class="flex flex-col gap-4">
                    {{-- Aksi Cetak --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <h4 class="text-sm font-bold text-slate-700 mb-3"><i class="fa-solid fa-print text-slate-400 mr-2"></i>Cetak Ulang</h4>
                        <div class="space-y-3">
                            <button type="button" onclick="printUSB()" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-sm font-bold transition-all shadow-md shadow-blue-500/20">
                                <i class="fa-solid fa-print"></i> Printer USB / Default
                            </button>
                            <button type="button" onclick="printBluetooth('{{ $selectedOrder->id }}')" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-indigo-100 hover:border-indigo-600 hover:bg-indigo-50 text-indigo-700 rounded-xl text-sm font-bold transition-all">
                                <i class="fa-brands fa-bluetooth text-lg"></i> Printer Bluetooth
                            </button>
                        </div>
                    </div>

                    {{-- Frame Struk --}}
                    <div class="bg-slate-800 p-4 rounded-2xl shadow-inner flex-1 flex flex-col items-center justify-start overflow-hidden relative">
                        <div class="absolute inset-0 bg-[radial-gradient(#334155_1px,transparent_1px)] [background-size:16px_16px] opacity-30"></div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 relative z-10">Live Preview Struk</p>
                        
                        <div class="bg-white shadow-xl relative z-10 flex-1 flex justify-center py-2 rounded" style="width: 64mm; overflow: hidden; min-height: 250px;" id="receipt-preview-container">
                            <iframe src="{{ route('kasir.struk', $selectedOrder->id) }}" id="receipt-iframe" class="border-none pointer-events-none" style="width: 58mm; min-height: 400px;"></iframe>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="bg-white px-6 py-4 border-t border-slate-100 flex justify-end">
                <button wire:click="closeDetail" class="px-8 py-2.5 text-sm font-bold text-slate-600 bg-white border-2 border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-xl transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    // FUNGSI PRINT STRUK (SAMA DENGAN POS CHECKOUT)
    function printUSB() {
        const iframe = document.getElementById('receipt-iframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }

    async function printBluetooth(orderId) {
        try {
            const device = await navigator.bluetooth.requestDevice({
                filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }],
                optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
            });
            const server = await device.gatt.connect();
            const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
            const characteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

            const iframe = document.getElementById('receipt-iframe');
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            let textToPrint = iframeDoc.body.innerText || 'Struk Kosong\n';
            textToPrint += "\n\n\n";

            const encoder = new TextEncoder();
            const data = encoder.encode(textToPrint);

            const CHUNK_SIZE = 100;
            for (let i = 0; i < data.length; i += CHUNK_SIZE) {
                const chunk = data.slice(i, i + CHUNK_SIZE);
                await characteristic.writeValue(chunk);
            }

            Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Struk dicetak!', showConfirmButton: false, timer: 2000});
        } catch (error) {
            console.error('Bluetooth Print Error:', error);
            Swal.fire('Error Bluetooth', 'Gagal koneksi ke printer Bluetooth.', 'error');
        }
    }
    </script>
    @endpush
</div>
