<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'token' => \App\Http\Middleware\CheckToken::class,
            'medico' => \App\Http\Middleware\CheckMedico::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Errores fuera de los controladores (ruta inexistente, método incorrecto,
        // demasiadas peticiones...) también con la respuesta unificada.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return \App\Http\Controllers\Controller::response(
                    false,
                    $e->getStatusCode() === 404 ? 'Ruta no encontrada.' : ($e->getMessage() ?: 'Error en la petición.'),
                    null,
                    $e->getStatusCode(),
                );
            }
        });
    })->create();
