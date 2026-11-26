<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CheckToken
{
    /**
     * Deja pasar solo las peticiones con un token válido, enviado en la
     * cabecera "Authorization: Bearer <token>" o en el parámetro "token".
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $valor = $request->bearerToken() ?? $request->input('token');

            if (! $valor) {
                return Controller::response(false, 'Falta el token de acceso.', null, 401);
            }

            $token = Token::with('user')->where('token', $valor)->first();

            if (! $token || ! $token->user) {
                return Controller::response(false, 'Token no válido.', null, 401);
            }

            // Guardamos el usuario y el token para usarlos en los controladores.
            $request->setUserResolver(fn () => $token->user);
            $request->attributes->set('token', $token);
        } catch (Throwable $e) {
            report($e);

            return Controller::response(false, 'Error al comprobar el token.', null, 500);
        }

        return $next($request);
    }
}
