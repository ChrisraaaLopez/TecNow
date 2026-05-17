<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'esAdmin'      => \App\Http\Middleware\EsAdmin::class,
            'is_admin'     => \App\Http\Middleware\IsAdmin::class,
            'noSuspendido' => \App\Http\Middleware\CheckSuspendido::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejar error de BD dormida (ProxySQL cold-start en Laravel Cloud)
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if (str_contains($e->getMessage(), '9001') || str_contains($e->getMessage(), 'Max connect timeout')) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Base de datos iniciando, por favor intenta de nuevo en unos segundos.'], 503);
                }
                return response('<html><body style="font-family:sans-serif;text-align:center;padding:80px;background:#0f172a;color:#fff">
                    <h2>⏳ Iniciando servidor...</h2>
                    <p>La base de datos está despertando. <strong>Recargando automáticamente...</strong></p>
                    <meta http-equiv="refresh" content="15">
                    </body></html>', 503);
            }
        });
    })->create();
