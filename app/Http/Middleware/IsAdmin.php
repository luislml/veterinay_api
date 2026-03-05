<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        \Log::info('Middleware IsAdmin ejecutado');

        // Aquí tu lógica de administrador
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return $next($request);
    }
}
