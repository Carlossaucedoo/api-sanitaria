<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'rol', 'fecha_nacimiento', 'peso', 'altura', 'alergias',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'fecha_nacimiento' => 'date:Y-m-d',
            'peso' => 'float',
            'altura' => 'float',
        ];
    }

    /**
     * Tokens de acceso del usuario.
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Entradas del historial médico (cuando el usuario es paciente).
     */
    public function historial(): HasMany
    {
        return $this->hasMany(Historial::class, 'paciente_id')->orderByDesc('fecha');
    }

    public function esMedico(): bool
    {
        return $this->rol === 'medico';
    }
}
