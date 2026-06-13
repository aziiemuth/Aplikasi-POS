<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — Fase 2.1: Pembatasan Hak Akses Strict RBAC
 *
 * Cara penggunaan di route:
 *   ->middleware('role:admin')       // hanya admin
 *   ->middleware('role:kasir')       // hanya kasir
 *   ->middleware('role:admin,kasir') // admin DAN kasir boleh akses
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Cek apakah akun aktif
        if (! $user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi Administrator.');
        }

        // Cek apakah role user ada di daftar yang diizinkan
        if (! in_array($user->role, $roles)) {
            // Catat percobaan akses tidak sah
            ActivityLog::log(
                'Akses Ditolak',
                "User [{$user->username}] mencoba mengakses [{$request->url()}] yang bukan haknya.",
            );

            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
