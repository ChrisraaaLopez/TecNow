<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendido
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->activo == false) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Tu cuenta está suspendida.'], 403);
            }
            return back()->with('error', 'Tu cuenta está suspendida y no puedes realizar esta acción.');
        }

        return $next($request);
    }
}
