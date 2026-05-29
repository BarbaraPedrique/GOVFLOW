<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlujoTrabajo extends Model
{
    protected $table = 'flujos_trabajo';

    protected $fillable = [
        'user_id',
        'codigo',
        'nombre',
        'departamento',
        'estado',
        'fecha_limite',
        'fecha_completado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_limite' => 'date',
            'fecha_completado' => 'date',
        ];
    }

    public static function generarCodigo(): string
    {
        $ultimo = self::latest('id')->first();
        $numero = $ultimo ? intval(substr($ultimo->codigo, 3)) + 1 : 1;
        return 'WF-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCompletadoATiempoAttribute(): ?bool
    {
        if (!$this->fecha_limite || !$this->fecha_completado) {
            return null;
        }
        return $this->fecha_completado <= $this->fecha_limite;
    }
}
