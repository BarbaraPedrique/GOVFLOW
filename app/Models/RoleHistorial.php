<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleHistorial extends Model
{
    protected $table = 'role_historial';

    protected $fillable = [
        'user_id',
        'role_id',
        'asignado_en',
    ];

    protected function casts(): array
    {
        return [
            'asignado_en' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
