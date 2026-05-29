<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $table = 'horarios';

    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'titulo',
        'color',
        'ubicacion',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
