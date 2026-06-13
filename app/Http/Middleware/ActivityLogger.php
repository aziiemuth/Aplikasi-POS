<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ActivityLogger — Fase 2.1: Audit Trail Otomatis
 *
 * Middleware ini mencatat setiap request penting (POST/PUT/PATCH/DELETE)
 * ke tabel activity_logs untuk keperluan audit oleh Admin/Owner.
 *
 * Catatan: GET request tidak dicatat agar tidak membanjiri log table.
 */
class ActivityLogger
{
    /**
     * Aksi-aksi yang WAJIB dicatat meskipun GET.
     */
    protected array $alwaysLogRoutes = [
        'login',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Catat hanya jika user sudah login
        if (! auth()->check()) {
            return $response;
        }

        // Abaikan request otomatis dari background service seperti Livewire
        if ($request->is('livewire*') || $request->is('_debugbar*')) {
            return $response;
        }

        // Catat setiap metode yang mengubah data
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    private function logActivity(Request $request, Response $response): void
    {
        try {
            $routeName  = $request->route()?->getName() ?? 'unknown';
            $method     = $request->method();
            $url        = $request->path();

            ActivityLog::log(
                aksi: "{$method} {$url}",
                deskripsi: "Route: {$routeName} | Status: {$response->getStatusCode()}",
            );
        } catch (\Throwable $e) {
            // Jangan sampai error logging menghentikan request utama
            \Illuminate\Support\Facades\Log::warning('ActivityLogger gagal: ' . $e->getMessage());
        }
    }
}
