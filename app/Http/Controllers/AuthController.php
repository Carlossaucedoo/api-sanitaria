<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    /**
     * Registra un paciente nuevo. Los médicos solo se crean desde el seeder.
     */
    public function registro(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|min:3|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|max:255',
                'fecha_nacimiento' => 'nullable|date|before:today',
                'peso' => 'nullable|numeric|between:1,500',
                'altura' => 'nullable|numeric|between:30,250',
                'alergias' => 'nullable|string|max:1000',
            ]);

            $user = User::create([...$data, 'rol' => 'paciente']);

            return self::response(true, 'Paciente registrado correctamente.', $user, 201);
        } catch (ValidationException $e) {
            return self::response(false, 'Los datos no son válidos.', $e->errors(), 422);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al registrar el usuario.', null, 500);
        }
    }

    /**
     * Comprueba las credenciales y devuelve un token único nuevo.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credenciales = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $credenciales['email'])->first();

            if (! $user || ! Hash::check($credenciales['password'], $user->password)) {
                return self::response(false, 'Credenciales incorrectas.', null, 401);
            }

            $token = Token::create([
                'user_id' => $user->id,
                'token' => Str::random(60),
            ]);

            return self::response(true, 'Login correcto.', [
                'token' => $token->token,
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            return self::response(false, 'Los datos no son válidos.', $e->errors(), 422);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al iniciar sesión.', null, 500);
        }
    }

    /**
     * Borra el token con el que se ha hecho la petición.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->attributes->get('token')->delete();

            return self::response(true, 'Sesión cerrada: el token ya no es válido.');
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al cerrar la sesión.', null, 500);
        }
    }
}
