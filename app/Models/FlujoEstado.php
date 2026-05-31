<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlujoEstado extends Model
{
    protected $table = 'flujo_estados';

    protected $fillable = [
        'flujo_trabajo_id',
        'nombre',
        'orden',
        'actores',
        'actividades',
        'reglas',
        'rutas',
    ];

    protected function casts(): array
    {
        return [
            'actores'     => 'array',
            'actividades' => 'array',
            'reglas'      => 'array',
            'rutas'       => 'array',
        ];
    }

    public function flujoTrabajo(): BelongsTo
    {
        return $this->belongsTo(FlujoTrabajo::class, 'flujo_trabajo_id');
    }
}
