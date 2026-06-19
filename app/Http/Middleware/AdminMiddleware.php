<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si no está logueado o no es Admin, bloqueamos el acceso
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado. Solo los administradores pueden realizar esta acción.');
        }

        return $next($request);
    }
}