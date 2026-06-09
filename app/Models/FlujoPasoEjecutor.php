<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlujoPasoEjecutor extends Model
{
    protected $table = 'flujo_paso_ejecutores';

    protected $fillable = [
        'flujo_paso_asignacion_id',
        'user_id',
        'estado',
        'completado_en',
        'archivo',
        'mensaje',
    ];

    protected function casts(): array
    {
        return [
            'completado_en' => 'datetime',
        ];
    }

    public function pasoAsignacion(): BelongsTo
    {
        return $this->belongsTo(FlujoPasoAsignacion::class, 'flujo_paso_asignacion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
