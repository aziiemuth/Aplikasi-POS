<?php

namespace App\Livewire\Kasir;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class RiwayatTransaksi extends Component
{
    use WithPagination;

    public $tanggal;
    public $bulan;
    public $tahun;

    public $showDetailModal = false;
    public $selectedOrder = null;

    protected $queryString = ['tanggal', 'bulan', 'tahun'];

    public function mount()
    {
        // Default ke hari ini jika tidak ada filter
        if (!$this->tanggal && !$this->bulan && !$this->tahun) {
            $this->tanggal = date('d');
            $this->bulan = date('m');
            $this->tahun = date('Y');
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['tanggal', 'bulan', 'tahun'])) {
            $this->resetPage();
        }
    }

    public function viewDetail($orderId)
    {
        $this->selectedOrder = Order::with(['items.product', 'user'])->find($orderId);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    public function render()
    {
        $query = Order::query()->with('user');

        // Jika bukan admin, hanya bisa melihat riwayat transaksinya sendiri
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        // Filter status lunas
        $query->where('status', 'lunas');

        if ($this->tanggal) {
            $query->whereDay('created_at', $this->tanggal);
        }
        if ($this->bulan) {
            $query->whereMonth('created_at', $this->bulan);
        }
        if ($this->tahun) {
            $query->whereYear('created_at', $this->tahun);
        }

        // Urutkan dari yang terbaru
        $query->orderByDesc('created_at');

        return view('livewire.kasir.riwayat-transaksi', [
            'orders' => $query->paginate(15)
        ])->layout('layouts.app');
    }
}
