<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const STATUS_PENDIENTE = 'pendiente';
    const STATUS_ACTIVO = 'activo';
    const STATUS_INACTIVO = 'inactivo';

    protected $fillable = [
        'name',
        'apodo',
        'email',
        'password',
        'role_id',
        'status',
        'fecha_nacimiento',
        'descripcion',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function flujosTrabajo()
    {
        return $this->hasMany(FlujoTrabajo::class);
    }

    public function equipos(): BelongsToMany
    {
        return $this->belongsToMany(Equipo::class, 'equipo_user')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function equiposComoLider(): BelongsToMany
    {
        return $this->equipos()->wherePivot('rol', 'lider_equipo');
    }

    public function equiposComoEmpleado(): BelongsToMany
    {
        return $this->equipos()->wherePivot('rol', 'empleado');
    }

    public function equiposDirigidos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'gerente_id');
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'administrador';
    }

    public function isGerente(): bool
    {
        return $this->role?->slug === 'gerente';
    }

    public function isEmpleado(): bool
    {
        return $this->role?->slug === 'empleado';
    }

    public function isActivo(): bool
    {
        return $this->status === self::STATUS_ACTIVO;
    }

    public function isPendiente(): bool
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function roleLabel(): string
    {
        return $this->role?->name ?? 'Sin rol';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVO    => 'Activo',
            self::STATUS_INACTIVO  => 'Inactivo',
            self::STATUS_PENDIENTE => 'Pendiente',
            default                => ucfirst($this->status ?? ''),
        };
    }
}
