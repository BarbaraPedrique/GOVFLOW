<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAuditoria extends Model
{
    const UPDATED_AT = null;

    protected $table = 'logs_auditoria';

    protected $fillable = [
        'user_id',
        'accion',
        'entidad_type',
        'entidad_id',
        'descripcion',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'json',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(
        string $accion,
        string $entidadType,
        ?int $entidadId,
        string $descripcion,
        ?array $metadata = null
    ): self {
        return static::create([
            'user_id' => request()?->user()?->id,
            'accion' => $accion,
            'entidad_type' => $entidadType,
            'entidad_id' => $entidadId,
            'descripcion' => $descripcion,
            'metadata' => $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
