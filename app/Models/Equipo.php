<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'gerente_id',
        'created_by',
    ];

    public function gerente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerente_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'equipo_user')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function lideres(): BelongsToMany
    {
        return $this->miembros()->wherePivot('rol', 'lider_equipo');
    }

    public function empleados(): BelongsToMany
    {
        return $this->miembros()->wherePivot('rol', 'empleado');
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }
}
