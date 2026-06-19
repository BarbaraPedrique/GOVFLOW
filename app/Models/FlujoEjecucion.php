<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlujoEjecucion extends Model
{
    protected $table = 'flujo_ejecuciones';

    protected $fillable = [
        'flujo_trabajo_id',
        'flujo_codigo',
        'flujo_nombre',
        'estado',
        'paso_actual_index',
    ];

    public function flujoTrabajo(): BelongsTo
    {
        return $this->belongsTo(FlujoTrabajo::class);
    }

    public function pasos(): HasMany
    {
        return $this->hasMany(FlujoPasoAsignacion::class, 'flujo_ejecucion_id')->orderBy('paso_index');
    }

    public function pasoActual()
    {
        return $this->hasOne(FlujoPasoAsignacion::class, 'flujo_ejecucion_id')
            ->where('paso_index', $this->paso_actual_index);
    }
}
