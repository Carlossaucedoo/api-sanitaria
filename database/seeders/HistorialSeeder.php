<?php

namespace Database\Seeders;

use App\Models\Historial;
use App\Models\User;
use Illuminate\Database\Seeder;

class HistorialSeeder extends Seeder
{
    /**
     * Entradas de historial de los pacientes de prueba.
     */
    public function run(): void
    {
        $medico = User::where('email', 'medico@salud.test')->first();
        $ana = User::where('email', 'ana@salud.test')->first();
        $javier = User::where('email', 'javier@salud.test')->first();

        $entradas = [
            [$ana, '2026-03-10', 'Fiebre y dolor de garganta', 'Faringitis vírica', 'Paracetamol 1 g cada 8 horas durante 5 días', 'Evitar amoxicilina por alergia a la penicilina.'],
            [$ana, '2026-09-02', 'Revisión anual', 'Paciente sana', 'Sin tratamiento', null],
            [$javier, '2026-05-18', 'Dolor lumbar tras esfuerzo', 'Lumbalgia mecánica', 'Ibuprofeno 600 mg cada 8 horas y reposo relativo', 'Revisar en 2 semanas si no mejora.'],
        ];

        foreach ($entradas as [$paciente, $fecha, $motivo, $diagnostico, $tratamiento, $observaciones]) {
            Historial::create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha' => $fecha,
                'motivo' => $motivo,
                'diagnostico' => $diagnostico,
                'tratamiento' => $tratamiento,
                'observaciones' => $observaciones,
            ]);
        }
    }
}
