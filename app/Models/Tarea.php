<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarea extends Model
{
    protected $table = 'tareas';

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'prioridad',
        'categoria',
        'fecha_vencimiento',
        'completada',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'completada' => 'boolean',
            'fecha_vencimiento' => 'date',
            'orden' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePorPrioridad($query)
    {
        return $query->orderByRaw("FIELD(prioridad, 'alta', 'media', 'baja')")
            ->orderBy('orden')
            ->orderBy('fecha_vencimiento');
    }
}
