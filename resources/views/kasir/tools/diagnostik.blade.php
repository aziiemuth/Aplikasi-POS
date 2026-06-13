@extends('layouts.app')

@section('page-title', 'Diagnostik Alat Kasir')
@section('page-subtitle', 'Uji fungsi Scanner dan Printer (USB & Bluetooth)')

@section('content')
<style>
@media print {
    @page { margin: 0; }
    body { background: #ffffff !important; }
    body * { visibility: hidden !important; }
    #print-area, #print-area * { visibility: visible !important; }
    #print-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 58mm !important;
        margin: 0 !important;
        padding: 4px !important;
        border: none !important;
        background: #ffffff !important;
        box-shadow: none !important;
    }
}
</style>
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- TESTER SCANNER --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-200">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mb-4">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Test Barcode Scanner</h3>
            <p class="text-slate-500 text-sm mb-4">Arahkan kursor ke kolom di bawah ini dan lakukan pemindaian menggunakan barcode scanner fisik (USB/Bluetooth).</p>

            <input type="text" id="scanner-test-input" autofocus autocomplete="off"
                   class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all"
                   placeholder="Scan barcode di sini..."
                   oninput="onScannerTest(this.value)">

            <div class="mt-4 min-h-[40px] px-4 py-2 bg-slate-50 rounded-lg border border-slate-100 flex items-center text-sm" id="scanner-test-result">
                <span class="text-slate-400 italic">Menunggu input scanner...</span>
            </div>
        </div>

        {{-- TESTER PRINTER --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-200">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl mb-4">
                <i class="fa-solid fa-print"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Test Printer Thermal</h3>
            <p class="text-slate-500 text-sm mb-4">Klik tombol di bawah untuk mencetak struk percobaan ke printer Anda.</p>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="testPrinterUSB()" class="py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors flex justify-center items-center gap-2">
                    <i class="fa-solid fa-print"></i> Print USB
                </button>
                <button onclick="testPrinterBluetooth()" class="py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition-colors flex justify-center items-center gap-2">
                    <i class="fa-brands fa-bluetooth-b"></i> Bluetooth
                </button>
            </div>

            {{-- Printer Dummy Area (Hidden by default) --}}
            <div id="printer-dummy-area" class="hidden mt-4 bg-white border border-dashed border-slate-300 p-4 rounded-xl">
                <div id="print-area" class="w-full max-w-[58mm] mx-auto bg-slate-50 border border-slate-200 p-3 shadow-sm font-mono text-[10px] leading-tight text-slate-800">
                    <div class="text-center font-bold text-sm mb-1">TOKO DEMO</div>
                    <div class="text-center mb-2">Jl. Contoh No. 123<br>0812-3456-7890</div>
                    <div class="border-t border-dashed border-slate-400 my-1"></div>
                    <div>Tgl: {{ now()->format('d/m/Y H:i') }}</div>
                    <div>Kasir: {{ auth()->user()->name }}</div>
                    <div class="border-t border-dashed border-slate-400 my-1"></div>
                    <div class="flex justify-between"><span>Produk A</span><span>10.000</span></div>
                    <div class="flex justify-between"><span>Produk B</span><span>15.000</span></div>
                    <div class="border-t border-dashed border-slate-400 my-1"></div>
                    <div class="flex justify-between font-bold"><span>TOTAL</span><span>25.000</span></div>
                    <div class="border-t border-dashed border-slate-400 my-1"></div>
                    <div class="text-center mt-2">Terima Kasih</div>
                </div>
            </div>
        </div>
    </div>
    
    <audio id="beep-sound" src="{{ asset('scanner.mp3') }}" preload="auto"></audio>

</div>

@push('scripts')
<script>
    let scanTestTimer = null;
    function onScannerTest(val) {
        clearTimeout(scanTestTimer);
        const resEl = document.getElementById('scanner-test-result');
        if (val.length === 0) {
            resEl.innerHTML = '<span class="text-slate-400 italic">Menunggu input scanner...</span>';
            return;
        }

        resEl.textContent = `⏳ Mendeteksi... (${val.length} karakter)`;

        scanTestTimer = setTimeout(() => {
            const beep = document.getElementById('beep-sound');
            if (beep) { beep.currentTime = 0; beep.play().catch(() => {}); }
            resEl.innerHTML = `<span class="text-emerald-600 font-semibold"><i class="fa-solid fa-check-circle mr-1"></i> Scanner OK: <code class="font-mono bg-emerald-50 px-1 py-0.5 rounded border border-emerald-200 text-emerald-700">${val}</code></span>`;
            
            setTimeout(() => {
                document.getElementById('scanner-test-input').value = '';
                resEl.innerHTML = '<span class="text-slate-400 italic">Menunggu input scanner...</span>';
            }, 2000);
        }, 200);
    }

    function testPrinterUSB() {
        const area = document.getElementById('printer-dummy-area');
        area.classList.remove('hidden');
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Memproses Struk...',
            showConfirmButton: false,
            timer: 2000
        });

        setTimeout(() => window.print(), 500);
    }

    async function testPrinterBluetooth() {
        try {
            const device = await navigator.bluetooth.requestDevice({
                filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }],
                optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
            });
            const server = await device.gatt.connect();
            const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
            const characteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');

            const text = "TOKO DEMO\nJl. Contoh No. 123\n--------------------------------\nTest Printer Bluetooth OK!\n" + new Date().toLocaleString() + "\n--------------------------------\nTerima Kasih\n\n\n";
            const encoder = new TextEncoder();
            const data = encoder.encode(text);

            for (let i = 0; i < data.length; i += 100) {
                await characteristic.writeValue(data.slice(i, i + 100));
            }
            Swal.fire('Berhasil', 'Printer Bluetooth terhubung & mencetak!', 'success');
        } catch (error) {
            console.error('Bluetooth Print Error:', error);
            Swal.fire('Gagal Pairing', 'Pastikan bluetooth menyala dan printer mendukung Web Bluetooth.', 'error');
        }
    }
</script>
@endpush
@endsection
