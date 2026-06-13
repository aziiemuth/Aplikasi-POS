<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * AuthController — Fase 2.3: Login + Rate Limiting + Session Security
 */
class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    /**
     * Proses login dengan proteksi brute-force (Rate Limiting).
     * Maksimal 5x percobaan gagal per IP per menit.
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // === PROTEKSI BRUTE-FORCE ===
        $throttleKey = 'login-attempt:' . $request->ip() . '|' . strtolower($request->username);

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            // Catat percobaan brute force
            ActivityLog::log(
                'Login Diblokir',
                "IP [{$request->ip()}] diblokir karena terlalu banyak percobaan login gagal untuk username [{$request->username}]."
            );

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login gagal. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // === PROSES AUTENTIKASI ===
        $credentials = [
            'username'  => $request->username,
            'password'  => $request->password,
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            // Tambah hitungan percobaan gagal (timeout: 5 menit)
            RateLimiter::hit($throttleKey, 300);

            $attemptsLeft = 5 - RateLimiter::attempts($throttleKey);

            // Catat login gagal
            ActivityLog::log(
                'Login Gagal',
                "Percobaan login gagal untuk username [{$request->username}] dari IP [{$request->ip()}]."
            );

            throw ValidationException::withMessages([
                'username' => "Username atau password salah. Sisa percobaan: {$attemptsLeft}x.",
            ]);
        }

        // === LOGIN BERHASIL ===
        RateLimiter::clear($throttleKey); // Reset hitungan gagal
        $request->session()->regenerate();

        $user = Auth::user();

        // Catat login berhasil ke activity log
        ActivityLog::log(
            'Login Berhasil',
            "User [{$user->username}] (Role: {$user->role}) berhasil login dari IP [{$request->ip()}]."
        );

        return $this->redirectByRole();
    }

    /**
     * Proses logout dan catat aktivitas.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log(
                'Logout',
                "User [{$user->username}] logout dari sistem."
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout. Sampai jumpa!');
    }

    /**
     * Redirect user ke dashboard sesuai role-nya.
     */
    private function redirectByRole()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('kasir.pos');
    }
}
