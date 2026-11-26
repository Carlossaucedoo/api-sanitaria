<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/registro', [AuthController::class, 'registro'])->name('auth.registro');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Rutas con token (pacientes y médicos)
Route::middleware('token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/mi-ficha', [PacienteController::class, 'miFicha'])->name('paciente.ficha');
    Route::patch('/mi-ficha', [PacienteController::class, 'actualizarBiometria'])->name('paciente.biometria');

    // Rutas de gestión médica (token + rol médico)
    Route::middleware('medico')->prefix('pacientes')->group(function () {
        Route::get('/', [MedicoController::class, 'pacientes'])->name('medico.pacientes');
        Route::get('/{id}', [MedicoController::class, 'fichaPaciente'])->whereNumber('id')->name('medico.ficha');
        Route::post('/{id}/historial', [MedicoController::class, 'nuevaEntrada'])->whereNumber('id')->name('medico.historial');
        Route::patch('/{id}/alergias', [MedicoController::class, 'actualizarAlergias'])->whereNumber('id')->name('medico.alergias');
    });
});
