<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMedico
{
    /**
     * Deja pasar solo a los médicos. Va después de CheckToken,
     * así que el usuario ya está identificado.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->esMedico()) {
            return Controller::response(false, 'Acceso denegado: solo los médicos pueden usar esta ruta.', null, 403);
        }

        return $next($request);
    }
}
