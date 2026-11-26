<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class PacienteController extends Controller
{
    /**
     * Devuelve los datos médicos y el historial del usuario del token.
     */
    public function miFicha(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load('historial.medico:id,name,email');

            return self::response(true, 'Ficha médica de '.$user->name.'.', $user);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al obtener la ficha médica.', null, 500);
        }
    }

    /**
     * Actualiza el peso y/o la altura del usuario del token.
     */
    public function actualizarBiometria(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'peso' => 'required_without:altura|numeric|between:1,500',
                'altura' => 'required_without:peso|numeric|between:30,250',
            ]);

            $user = $request->user();
            $user->update($data);

            return self::response(true, 'Datos biométricos actualizados.', $user->only('id', 'name', 'peso', 'altura'));
        } catch (ValidationException $e) {
            return self::response(false, 'Los datos no son válidos.', $e->errors(), 422);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al actualizar los datos biométricos.', null, 500);
        }
    }
}
