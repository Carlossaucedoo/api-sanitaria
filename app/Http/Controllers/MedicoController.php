<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class MedicoController extends Controller
{
    /**
     * Lista de pacientes (para saber sus IDs).
     */
    public function pacientes(): JsonResponse
    {
        try {
            $pacientes = User::where('rol', 'paciente')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'fecha_nacimiento']);

            return self::response(true, 'Lista de pacientes.', $pacientes);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al obtener los pacientes.', null, 500);
        }
    }

    /**
     * Ficha completa e historial de un paciente.
     */
    public function fichaPaciente(int $id): JsonResponse
    {
        try {
            $paciente = $this->buscarPaciente($id)->load('historial.medico:id,name,email');

            return self::response(true, 'Ficha médica de '.$paciente->name.'.', $paciente);
        } catch (ModelNotFoundException $e) {
            return self::response(false, 'No existe ningún paciente con el id '.$id.'.', null, 404);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al obtener la ficha del paciente.', null, 500);
        }
    }

    /**
     * Añade una entrada nueva al historial del paciente, firmada por el médico del token.
     */
    public function nuevaEntrada(Request $request, int $id): JsonResponse
    {
        try {
            $paciente = $this->buscarPaciente($id);

            $data = $request->validate([
                'fecha' => 'nullable|date|before_or_equal:today',
                'motivo' => 'required|string|max:255',
                'diagnostico' => 'required|string|max:255',
                'tratamiento' => 'required|string|max:2000',
                'observaciones' => 'nullable|string|max:2000',
            ]);

            $entrada = Historial::create([
                ...$data,
                'fecha' => $data['fecha'] ?? now()->toDateString(),
                'paciente_id' => $paciente->id,
                'medico_id' => $request->user()->id,
            ]);

            return self::response(true, 'Entrada añadida al historial de '.$paciente->name.'.', $entrada->load('medico:id,name,email'), 201);
        } catch (ModelNotFoundException $e) {
            return self::response(false, 'No existe ningún paciente con el id '.$id.'.', null, 404);
        } catch (ValidationException $e) {
            return self::response(false, 'Los datos no son válidos.', $e->errors(), 422);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al añadir la entrada al historial.', null, 500);
        }
    }

    /**
     * Modifica las alergias del paciente.
     */
    public function actualizarAlergias(Request $request, int $id): JsonResponse
    {
        try {
            $paciente = $this->buscarPaciente($id);

            $data = $request->validate([
                'alergias' => 'present|nullable|string|max:1000',
            ]);

            $paciente->update(['alergias' => $data['alergias']]);

            return self::response(true, 'Alergias de '.$paciente->name.' actualizadas.', $paciente->only('id', 'name', 'alergias'));
        } catch (ModelNotFoundException $e) {
            return self::response(false, 'No existe ningún paciente con el id '.$id.'.', null, 404);
        } catch (ValidationException $e) {
            return self::response(false, 'Los datos no son válidos.', $e->errors(), 422);
        } catch (Throwable $e) {
            report($e);

            return self::response(false, 'Error al actualizar las alergias.', null, 500);
        }
    }

    /**
     * Busca un usuario con rol paciente o lanza ModelNotFoundException.
     */
    private function buscarPaciente(int $id): User
    {
        return User::where('rol', 'paciente')->findOrFail($id);
    }
}
