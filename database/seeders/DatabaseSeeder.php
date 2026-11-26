<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos de prueba de la API sanitaria.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            HistorialSeeder::class,
        ]);
    }
}
