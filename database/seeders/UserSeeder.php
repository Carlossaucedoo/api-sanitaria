<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * 1 médico y 2 pacientes. Contraseña de todos: password
     */
    public function run(): void
    {
        User::create([
            'name' => 'Dra. Laura Martín',
            'email' => 'medico@salud.test',
            'password' => 'password',
            'rol' => 'medico',
            'fecha_nacimiento' => '1980-04-12',
        ]);

        User::create([
            'name' => 'Ana García López',
            'email' => 'ana@salud.test',
            'password' => 'password',
            'rol' => 'paciente',
            'fecha_nacimiento' => '1995-06-21',
            'peso' => 62.5,
            'altura' => 165,
            'alergias' => 'Penicilina',
        ]);

        User::create([
            'name' => 'Javier Pérez Ruiz',
            'email' => 'javier@salud.test',
            'password' => 'password',
            'rol' => 'paciente',
            'fecha_nacimiento' => '1988-11-03',
            'peso' => 81,
            'altura' => 178,
            'alergias' => null,
        ]);
    }
}
