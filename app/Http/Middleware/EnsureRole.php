<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Hanya izinkan user dengan role tertentu,
     * mis. ->middleware('role:dokter,kasir').
     * Admin adalah superuser: selalu lolos semua pemeriksaan.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ($user->role !== 'admin' && ! in_array($user->role, $roles, true))) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
