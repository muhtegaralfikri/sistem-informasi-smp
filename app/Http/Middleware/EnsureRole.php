<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pastikan user memiliki salah satu role yang diizinkan.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role || ! in_array($user->role->name, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak untuk role ini.');
        }

        return $next($request);
    }
}
