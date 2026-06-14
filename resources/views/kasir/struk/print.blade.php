<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->nomor_order }}</title>
    <style>
        /* CSS Khusus Printer Thermal 58mm */
        @page {
            margin: 0;
            size: 58mm auto; /* Lebar kertas 58mm, tinggi otomatis */
        }
        
        body {
            margin: 0;
            padding: 5px; /* Kurangi padding agar area lebih luas */
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px; /* Sedikit lebih kecil agar muat banyak karakter */
            color: #000;
            background: #fff;
            width: 58mm; /* Lebar kertas standar 58mm */
            box-sizing: border-box;
            font-weight: 700; /* Bikin tebal agar tidak putus-putus */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Container untuk membungkus konten struk agar rapi ketika didownload PNG */
        .struk-container {
            width: 100%;
            max-width: 58mm;
            margin: 0 auto;
        }

        h1, h2, h3, h4, h5, p {
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        .header {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 10px;
        }

        .info {
            font-size: 10px;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 5px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        
        .items td {
            vertical-align: top;
            padding: 2px 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .item-name {
            display: block;
            margin-bottom: 2px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .summary {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-bottom: 10px;
            font-size: 11px;
        }
        
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary td {
            padding: 2px 0;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .barcode-container {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        /* Hilangkan elemen yang tidak perlu dicetak */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div id="print-area" class="struk-container">
        <!-- Header Toko -->
        <div class="header text-center">
            <h2>Toko Kita</h2>
            <p>Jl. Contoh Alamat No. 123<br>Telp: 08123456789</p>
        </div>

        <!-- Info Transaksi -->
        <div class="info">
            <table style="width: 100%;">
                <tr>
                    <td class="text-left">No.</td>
                    <td class="text-right">{{ $order->nomor_order }}</td>
                </tr>
                <tr>
                    <td class="text-left">Tgl</td>
                    <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Kasir</td>
                    <td class="text-right">{{ $order->user->name ?? 'Kasir' }}</td>
                </tr>
                <tr>
                    <td class="text-left">Cust</td>
                    <td class="text-right">{{ $order->nama_customer }}</td>
                </tr>
            </table>
        </div>

        <!-- Daftar Belanja -->
        <div class="items">
            <table>
                @foreach($order->items as $item)
                <tr>
                    <td colspan="3">
                        <span class="item-name bold">{{ $item->nama_produk_snapshot }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-left" style="width: 15%;">{{ $item->jumlah }}x</td>
                    <td class="text-left" style="width: 45%;">
                        {{ number_format($item->harga_jual_snapshot, 0, ',', '.') }}
                        @if($item->diskon_item > 0)
                            <br><small>(Disc: -{{ number_format($item->diskon_item, 0, ',', '.') }})</small>
                        @endif
                    </td>
                    <td class="text-right bold" style="width: 40%;">{{ number_format($item->total_harga_item, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="summary">
            <table>
                <tr>
                    <td class="text-left">Subtotal</td>
                    <td class="text-right">{{ number_format($order->total_sebelum_diskon, 0, ',', '.') }}</td>
                </tr>
                @if($order->diskon_global > 0)
                <tr>
                    <td class="text-left">Diskon</td>
                    <td class="text-right">-{{ number_format($order->diskon_global, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($order->pajak_ppn > 0)
                <tr>
                    <td class="text-left">Pajak/PPN</td>
                    <td class="text-right">{{ number_format($order->pajak_ppn, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td class="text-left bold">TOTAL</td>
                    <td class="text-right bold" style="font-size: 14px;">{{ number_format($order->total_pembayaran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left">Tunai/Bayar</td>
                    <td class="text-right">{{ number_format($order->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-left bold">KEMBALI</td>
                    <td class="text-right bold">{{ number_format($order->kembalian, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="bold">Terima Kasih!</p>
            <p>Barang yang sudah dibeli<br>tidak dapat dikembalikan</p>
        </div>
    </div>

    <!-- Script Autoprint jika diminta -->
    <script>
        // Cek parameter ?print=true di URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('print')) {
            window.onload = function() {
                window.print();
            }
        }
    </script>
</body>
</html>
