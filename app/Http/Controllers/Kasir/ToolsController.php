<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    /**
     * Tampilkan halaman diagnostik alat (tester scanner & printer) untuk kasir.
     */
    public function diagnostik()
    {
        return view('kasir.tools.diagnostik');
    }
}
