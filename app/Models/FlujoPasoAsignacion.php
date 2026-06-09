<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlujoPasoAsignacion extends Model
{
    protected $table = 'flujo_paso_asignaciones';

    protected $fillable = [
        'flujo_ejecucion_id',
        'paso_index',
        'paso_nombre',
        'asignado_a',
        'estado',
        'fecha_limite',
        'fecha_completado',
        'archivo',
        'mensaje',
        'completado_por',
        'revisor_id',
        'revision_estado',
        'revision_comentario',
        'revisado_por',
        'revisado_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha_limite'     => 'datetime',
            'fecha_completado' => 'datetime',
            'revisado_en'      => 'datetime',
        ];
    }

    public function ejecucion(): BelongsTo
    {
        return $this->belongsTo(FlujoEjecucion::class, 'flujo_ejecucion_id');
    }

    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    public function ejecutores(): HasMany
    {
        return $this->hasMany(FlujoPasoEjecutor::class, 'flujo_paso_asignacion_id');
    }

    public function ejecutoresCompletados(): HasMany
    {
        return $this->ejecutores()->where('estado', 'completado');
    }

    public function todosEjecutoresCompletados(): bool
    {
        $total = $this->ejecutores()->count();
        if ($total === 0) return false;
        return $this->ejecutoresCompletados()->count() === $total;
    }
}
