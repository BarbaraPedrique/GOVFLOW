<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    const UPDATED_AT = null;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'icono',
        'color',
        'url',
        'leido',
    ];

    protected function casts(): array
    {
        return [
            'leido' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('leido', false)
            ->where('created_at', '>=', now()->subDays(3));
    }

    public function scopeRecientes($query)
    {
        return $query->where('created_at', '>=', now()->subDays(3))
            ->orderByDesc('created_at');
    }
}
