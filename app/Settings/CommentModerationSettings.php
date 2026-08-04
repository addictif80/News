<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CommentModerationSettings extends Settings
{
    /** @var array<int, string> */
    public array $blocked_words;

    public static function group(): string
    {
        return 'comment_moderation';
    }
}
