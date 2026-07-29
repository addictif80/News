<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Widget extends Model
{
    protected $fillable = ['name', 'token', 'category_id', 'articles_count', 'theme'];

    protected static function booted(): void
    {
        static::creating(function (self $widget) {
            $widget->token ??= Str::random(24);
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function embedCode(): string
    {
        $src = route('widgets.script', $this->token);

        return sprintf('<script src="%s" async></script>', $src);
    }
}
