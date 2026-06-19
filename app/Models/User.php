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
    const STATUS_SUSPENDIDO = 'suspendido';

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

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
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

    public function getFotoUrlAttribute(): string
    {
        if (!$this->foto) {
            return 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80';
        }
        return asset('storage/' . $this->foto) . '?v=' . ($this->updated_at?->timestamp ?? 0);
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    public function roleHistorial(): HasMany
    {
        return $this->hasMany(RoleHistorial::class)->latest('asignado_en');
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
            self::STATUS_ACTIVO      => 'Activo',
            self::STATUS_INACTIVO    => 'Inactivo',
            self::STATUS_PENDIENTE   => 'Pendiente',
            self::STATUS_SUSPENDIDO  => 'Suspendido',
            default                  => ucfirst($this->status ?? ''),
        };
    }

    public function calcularEstrellasMes(?int $year = null, ?int $month = null): float
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $sessions = $this->sessions()
            ->whereYear('logged_in_at', $year)
            ->whereMonth('logged_in_at', $month)
            ->with('breaks')
            ->get();

        if ($sessions->isEmpty()) {
            $totalTareas = $this->tareas()
                ->where(function ($q) use ($year, $month) {
                    $q->whereYear('created_at', $year)->whereMonth('created_at', $month)
                      ->orWhere(function ($q) use ($year, $month) {
                          $q->whereYear('completed_at', $year)
                            ->whereMonth('completed_at', $month)
                            ->where('completada', true);
                      });
                })
                ->count();
            $completadas = $this->tareas()
                ->whereYear('completed_at', $year)
                ->whereMonth('completed_at', $month)
                ->where('completada', true)
                ->count();

            if ($totalTareas === 0) return 0;

            $tasa = $completadas / $totalTareas;
            return match (true) {
                $tasa >= 0.95 => 5.0,
                $tasa >= 0.85 => 4.0,
                $tasa >= 0.70 => 3.0,
                $tasa >= 0.50 => 2.0,
                $tasa >= 0.30 => 1.0,
                default => 0.5,
            };
        }

        $totalWorkSeconds = 0;
        $totalPenaltySeconds = 0;

        foreach ($sessions as $session) {
            $duration = $session->duration;
            if ($duration === null) continue;
            $totalWorkSeconds += $duration;

            foreach ($session->breaks as $break) {
                if ($break->break_end === null) continue;
                $breakMinutes = $break->break_start->diffInMinutes($break->break_end);
                if ($breakMinutes > 35) {
                    $totalPenaltySeconds += ($breakMinutes - 35) * 60;
                }
            }
        }

        if ($totalWorkSeconds <= 0) return 0;

        $efficiency = max(0, ($totalWorkSeconds - $totalPenaltySeconds) / $totalWorkSeconds);

        return match (true) {
            $efficiency >= 0.95 => 5.0,
            $efficiency >= 0.90 => 4.5,
            $efficiency >= 0.85 => 4.0,
            $efficiency >= 0.80 => 3.5,
            $efficiency >= 0.75 => 3.0,
            $efficiency >= 0.70 => 2.5,
            $efficiency >= 0.60 => 2.0,
            $efficiency >= 0.50 => 1.5,
            $efficiency >= 0.40 => 1.0,
            default => 0.5,
        };
    }
}
