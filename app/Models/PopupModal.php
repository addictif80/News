<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupModal extends Model
{
    protected $fillable = [
        'name', 'content', 'trigger_type', 'trigger_value', 'target_pages',
        'display_frequency_days', 'is_active', 'starts_at', 'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'target_pages' => 'array',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeCurrentlyActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
