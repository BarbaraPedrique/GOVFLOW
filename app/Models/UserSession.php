<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'logged_in_at',
        'logged_out_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at'  => 'datetime',
            'logged_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(SessionBreak::class, 'user_session_id');
    }

    public function activeBreaks(): HasMany
    {
        return $this->breaks()->whereNull('break_end');
    }

    public function getDurationAttribute(): ?int
    {
        $end = $this->logged_out_at ?? now();
        if (!$this->logged_in_at) return null;
        return $end->diffInSeconds($this->logged_in_at);
    }

    public function getTotalBreakSecondsAttribute(): int
    {
        return $this->breaks()
            ->whereNotNull('break_end')
            ->get()
            ->sum(fn ($b) => $b->break_start->diffInSeconds($b->break_end));
    }

    public function getActiveBreakSecondsAttribute(): int
    {
        $active = $this->activeBreaks()->first();
        if (!$active) return 0;
        return $active->break_start->diffInSeconds(now());
    }
}
