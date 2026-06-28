<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMutation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TokoKelontongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create(['role' => 'admin', 'name' => 'Admin Dummy']);
        }

        $kasir1 = User::firstOrCreate(
            ['username' => 'kasir1'],
            ['name' => 'Kasir Satu', 'email' => 'kasir1@toko.com', 'password' => \Illuminate\Support\Facades\Hash::make('password'), 'role' => 'kasir', 'is_active' => true]
        );
        $kasir2 = User::firstOrCreate(
            ['username' => 'kasir2'],
            ['name' => 'Kasir Dua', 'email' => 'kasir2@toko.com', 'password' => \Illuminate\Support\Facades\Hash::make('password'), 'role' => 'kasir', 'is_active' => true]
        );
        
        $usersSimulasi = [$user, $kasir1, $kasir2];

        // 1. Kategori
        $categoriesData = [
            'Sembako',
            'Minuman',
            'Makanan Ringan',
            'Bumbu Dapur',
            'Keperluan Mandi & Cuci',
            'Obat-obatan'
        ];

        $categories = [];
        foreach ($categoriesData as $catName) {
            $categories[$catName] = Category::firstOrCreate(['nama_kategori' => $catName]);
        }

        // 2. Supplier
        $suppliersData = [
            ['nama_supplier' => 'Agen Sembako Makmur', 'kontak' => '081233334444', 'alamat' => 'Pasar Induk'],
            ['nama_supplier' => 'Distributor Minuman Segar', 'kontak' => '085677778888', 'alamat' => 'Jl. Merdeka No 12'],
            ['nama_supplier' => 'Grosir Kebutuhan Harian', 'kontak' => '081122223333', 'alamat' => 'Komplek Pergudangan A1']
        ];

        $suppliers = [];
        foreach ($suppliersData as $supData) {
            $suppliers[] = Supplier::firstOrCreate(['nama_supplier' => $supData['nama_supplier']], $supData);
        }

        // 3. Produk (Realistis Toko Kelontong)
        $productsData = [
            // Sembako
            ['nama' => 'Beras Ramos 5kg', 'cat' => 'Sembako', 'sup' => 0, 'hpp' => 58000, 'jual' => 65000, 'stok' => 50, 'satuan' => 'sak'],
            ['nama' => 'Gula Pasir Gulaku 1kg', 'cat' => 'Sembako', 'sup' => 0, 'hpp' => 14000, 'jual' => 16000, 'stok' => 100, 'satuan' => 'kg'],
            ['nama' => 'Minyak Goreng Bimoli 2L', 'cat' => 'Sembako', 'sup' => 0, 'hpp' => 32000, 'jual' => 35000, 'stok' => 60, 'satuan' => 'pouch'],
            ['nama' => 'Telur Ayam Ras 1kg', 'cat' => 'Sembako', 'sup' => 0, 'hpp' => 26000, 'jual' => 29000, 'stok' => 30, 'satuan' => 'kg'],
            
            // Minuman
            ['nama' => 'Aqua Botol 600ml', 'cat' => 'Minuman', 'sup' => 1, 'hpp' => 2500, 'jual' => 3500, 'stok' => 120, 'satuan' => 'botol'],
            ['nama' => 'Teh Pucuk Harum 350ml', 'cat' => 'Minuman', 'sup' => 1, 'hpp' => 2800, 'jual' => 4000, 'stok' => 100, 'satuan' => 'botol'],
            ['nama' => 'Kopi Kapal Api Mix 1 Renceng', 'cat' => 'Minuman', 'sup' => 1, 'hpp' => 11000, 'jual' => 13000, 'stok' => 50, 'satuan' => 'renceng'],
            ['nama' => 'Susu Indomilk UHT 250ml', 'cat' => 'Minuman', 'sup' => 1, 'hpp' => 4500, 'jual' => 6000, 'stok' => 80, 'satuan' => 'kotak'],
            
            // Makanan Ringan
            ['nama' => 'Indomie Goreng Spesial', 'cat' => 'Makanan Ringan', 'sup' => 2, 'hpp' => 2700, 'jual' => 3500, 'stok' => 200, 'satuan' => 'bungkus'],
            ['nama' => 'Indomie Kuah Ayam Bawang', 'cat' => 'Makanan Ringan', 'sup' => 2, 'hpp' => 2600, 'jual' => 3500, 'stok' => 150, 'satuan' => 'bungkus'],
            ['nama' => 'Chitato Sapi Panggang 68g', 'cat' => 'Makanan Ringan', 'sup' => 2, 'hpp' => 9500, 'jual' => 12000, 'stok' => 40, 'satuan' => 'bungkus'],
            ['nama' => 'Biskuit Roma Kelapa 300g', 'cat' => 'Makanan Ringan', 'sup' => 2, 'hpp' => 9000, 'jual' => 11000, 'stok' => 45, 'satuan' => 'bungkus'],
            
            // Bumbu Dapur
            ['nama' => 'Kecap Bango 520ml', 'cat' => 'Bumbu Dapur', 'sup' => 2, 'hpp' => 21000, 'jual' => 24000, 'stok' => 30, 'satuan' => 'pouch'],
            ['nama' => 'Saos Sambal ABC 340ml', 'cat' => 'Bumbu Dapur', 'sup' => 2, 'hpp' => 13500, 'jual' => 16000, 'stok' => 35, 'satuan' => 'botol'],
            ['nama' => 'Royco Ayam Renceng (12x8g)', 'cat' => 'Bumbu Dapur', 'sup' => 2, 'hpp' => 4500, 'jual' => 6000, 'stok' => 80, 'satuan' => 'renceng'],
            
            // Keperluan Mandi & Cuci
            ['nama' => 'Sabun Mandi Lifebuoy Merah', 'cat' => 'Keperluan Mandi & Cuci', 'sup' => 2, 'hpp' => 3500, 'jual' => 4500, 'stok' => 100, 'satuan' => 'pcs'],
            ['nama' => 'Shampo Clear Sachet Renceng', 'cat' => 'Keperluan Mandi & Cuci', 'sup' => 2, 'hpp' => 9000, 'jual' => 12000, 'stok' => 60, 'satuan' => 'renceng'],
            ['nama' => 'Deterjen Rinso Anti Noda 770g', 'cat' => 'Keperluan Mandi & Cuci', 'sup' => 2, 'hpp' => 19000, 'jual' => 22000, 'stok' => 50, 'satuan' => 'bungkus'],
            ['nama' => 'Pepsodent White 190g', 'cat' => 'Keperluan Mandi & Cuci', 'sup' => 2, 'hpp' => 10500, 'jual' => 13000, 'stok' => 70, 'satuan' => 'tube'],
            
            // Obat-obatan
            ['nama' => 'Panadol Biru (Isi 10)', 'cat' => 'Obat-obatan', 'sup' => 2, 'hpp' => 9000, 'jual' => 11000, 'stok' => 40, 'satuan' => 'strip'],
            ['nama' => 'Tolak Angin Cair', 'cat' => 'Obat-obatan', 'sup' => 2, 'hpp' => 3500, 'jual' => 4500, 'stok' => 50, 'satuan' => 'sachet']
        ];

        $productModels = [];
        foreach ($productsData as $index => $item) {
            // Generate SKU: TK-001
            $sku = 'TK-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            
            $product = Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'category_id' => $categories[$item['cat']]->id,
                    'nama_produk' => $item['nama'],
                    'satuan' => $item['satuan'],
                    'modal_hpp' => $item['hpp'],
                    'harga_jual' => $item['jual'],
                    'stok_saat_ini' => $item['stok'], // Stok saat ini
                    'stok_minimum' => 10,
                    'is_active' => true
                ]
            );
            $productModels[] = $product;

            // Buat Stok Masuk (Mutasi Stok Awal)
            // Cek apakah mutasi masuk untuk produk ini sudah ada untuk menghindari double stok saat run berulang
            $existsMutation = StockMutation::where('product_id', $product->id)->where('tipe', 'masuk')->where('keterangan', 'Stok Awal dari Sistem (Dummy)')->exists();
            if (!$existsMutation) {
                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'supplier_id' => $suppliers[$item['sup']]->id,
                    'tipe' => 'masuk',
                    'jumlah' => $item['stok'],
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $item['stok'],
                    'harga_beli' => $item['hpp'],
                    'keterangan' => 'Stok Awal dari Sistem (Dummy)',
                    'created_at' => Carbon::now()->subDays(30) // Anggap masuk 30 hari lalu
                ]);
            }
        }

        // 5. Buat Riwayat Transaksi Penjualan
        // Kita buat sekitar 30 transaksi selama 30 hari ke belakang
        $totalOrders = 30;
        
        for ($i = 0; $i < $totalOrders; $i++) {
            $randomUser = collect($usersSimulasi)->random();
            
            if ($i < 10) {
                // 10 transaksi dipastikan hari ini agar Omzet dan Laba hari ini terisi
                $orderDate = Carbon::now()->subHours(rand(0, 10))->subMinutes(rand(1, 59));
            } else {
                $orderDate = Carbon::now()->subDays(rand(1, 29))->subHours(rand(1, 12))->subMinutes(rand(1, 59));
            }
            
            // 1 sampai 5 jenis produk per order
            $numOfItems = rand(1, 5);
            $orderProducts = collect($productModels)->random($numOfItems);
            
            $totalSebelumDiskon = 0;
            $itemsData = [];

            foreach ($orderProducts as $prod) {
                $qty = rand(1, 3); // 1 sampai 3 qty per produk
                $harga_jual = (float) $prod->harga_jual;
                $hpp = (float) $prod->modal_hpp;
                $diskon_item = 0;
                $total_harga_item = ($harga_jual - $diskon_item) * $qty;

                $totalSebelumDiskon += $total_harga_item;

                $itemsData[] = [
                    'product_id' => $prod->id,
                    'product_model' => $prod,
                    'nama_produk_snapshot' => $prod->nama_produk,
                    'harga_jual_snapshot' => $harga_jual,
                    'hpp_snapshot' => $hpp,
                    'diskon_item' => $diskon_item,
                    'jumlah' => $qty,
                    'total_harga_item' => $total_harga_item,
                ];
            }

            $diskonGlobal = rand(0, 1) == 1 ? 0 : 5000; // Kadang ada diskon 5000, kadang tidak
            if ($totalSebelumDiskon <= $diskonGlobal) {
                $diskonGlobal = 0; // Jangan diskon lebih dari total
            }

            $pajakPpn = 0; // Kita anggap PPN 0 untuk kelontong

            $totalPembayaran = $totalSebelumDiskon - $diskonGlobal + $pajakPpn;
            
            // Simulasi jumlah uang pelanggan (dibulatkan ke ribuan/puluh ribuan terdekat)
            $pilihanUang = [
                ceil($totalPembayaran / 10000) * 10000, 
                ceil($totalPembayaran / 50000) * 50000,
                ceil($totalPembayaran / 100000) * 100000,
                $totalPembayaran
            ];
            $jumlahBayar = $pilihanUang[array_rand($pilihanUang)];
            if ($jumlahBayar < $totalPembayaran) $jumlahBayar = $totalPembayaran;
            
            $kembalian = $jumlahBayar - $totalPembayaran;

            $nomorOrder = 'INV-' . $orderDate->format('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Buat Order
            $order = Order::create([
                'user_id' => $randomUser->id,
                'nomor_order' => $nomorOrder,
                'nama_customer' => rand(0, 1) == 1 ? 'Umum' : 'Pelanggan ' . rand(1, 10),
                'total_sebelum_diskon' => $totalSebelumDiskon,
                'diskon_global' => $diskonGlobal,
                'pajak_ppn' => $pajakPpn,
                'total_pembayaran' => $totalPembayaran,
                'metode_pembayaran' => 'Tunai',
                'jumlah_bayar' => $jumlahBayar,
                'kembalian' => $kembalian,
                'status' => 'lunas',
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);

            // Buat Order Items & Mutasi Stok Keluar
            foreach ($itemsData as $itemD) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemD['product_id'],
                    'nama_produk_snapshot' => $itemD['nama_produk_snapshot'],
                    'harga_jual_snapshot' => $itemD['harga_jual_snapshot'],
                    'hpp_snapshot' => $itemD['hpp_snapshot'],
                    'diskon_item' => $itemD['diskon_item'],
                    'jumlah' => $itemD['jumlah'],
                    'total_harga_item' => $itemD['total_harga_item'],
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate
                ]);

                $prodModel = $itemD['product_model'];
                
                // Refresh model dari db
                $prodModel->refresh();
                $stokSisa = $prodModel->stok_saat_ini - $itemD['jumlah'];
                
                // Update stok di produk
                $prodModel->update(['stok_saat_ini' => $stokSisa]);

                // Buat mutasi
                StockMutation::create([
                    'product_id' => $prodModel->id,
                    'user_id' => $randomUser->id,
                    'order_id' => $order->id,
                    'tipe' => 'keluar',
                    'jumlah' => $itemD['jumlah'],
                    'stok_sebelum' => $prodModel->stok_saat_ini + $itemD['jumlah'], // Stok sebelum dikurangi
                    'stok_sesudah' => $stokSisa,
                    'keterangan' => 'Penjualan ' . $order->nomor_order,
                    'created_at' => $orderDate
                ]);
            }
        }

        // 6. Buat beberapa produk menjadi stok tipis (dibawah stok_minimum)
        $lowStockProducts = Product::inRandomOrder()->limit(4)->get();
        foreach($lowStockProducts as $lp) {
            $targetStok = rand(2, 8); // Stok tipis di bawah 10
            if ($lp->stok_saat_ini > $targetStok) {
                $diff = $lp->stok_saat_ini - $targetStok;
                $lp->update(['stok_saat_ini' => $targetStok]);
                
                StockMutation::create([
                    'product_id' => $lp->id,
                    'user_id' => $user->id,
                    'tipe' => 'keluar',
                    'jumlah' => $diff,
                    'stok_sebelum' => $lp->stok_saat_ini + $diff,
                    'stok_sesudah' => $targetStok,
                    'keterangan' => 'Penyesuaian stok (Simulasi Stok Tipis)',
                    'created_at' => Carbon::now()
                ]);
            }
        }
    }
}
