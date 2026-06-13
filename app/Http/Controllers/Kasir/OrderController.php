<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tampilkan struk cetak 58mm untuk transaksi tertentu.
     */
    public function struk(Order $order)
    {
        // Pastikan hanya user yang berwenang (Kasir/Admin) bisa melihat struk
        if (!auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $order->load(['items.product', 'user']);

        return view('kasir.struk.print', compact('order'));
    }
}
