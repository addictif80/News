<?php

namespace App\Support\Concerns;

use App\Models\ActivityLogEntry;
use Illuminate\Support\Str;

/**
 * Records who created/updated/deleted a model and which fields changed, for
 * the admin-facing audit log. Add `protected array $activityLogExcept = [...]`
 * on the model to keep large or noisy columns (e.g. rich content) out of the
 * stored diff — they're still tracked as "changed", just without the values.
 */
trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(fn ($model) => $model->recordActivity('created'));
        static::updated(fn ($model) => $model->recordActivity('updated'));
        static::deleted(fn ($model) => $model->recordActivity('deleted'));
    }

    public function recordActivity(string $event): void
    {
        $excluded = array_merge(
            ['id', 'created_at', 'updated_at', 'password', 'remember_token'],
            $this->activityLogExcept ?? [],
        );

        $changes = match ($event) {
            'created' => collect($this->getAttributes())
                ->except($excluded)
                ->map(fn ($value) => ['old' => null, 'new' => $this->summarizeValue($value)]),
            'updated' => collect($this->getChanges())
                ->except($excluded)
                ->mapWithKeys(fn ($value, $key) => [
                    $key => ['old' => $this->summarizeValue($this->getOriginal($key)), 'new' => $this->summarizeValue($value)],
                ]),
            default => collect(),
        };

        if ($event === 'updated' && $changes->isEmpty()) {
            return;
        }

        ActivityLogEntry::create([
            'causer_id' => auth()->id(),
            'causer_name' => auth()->user()?->name,
            'subject_type' => static::class,
            'subject_id' => $this->getKey(),
            'subject_label' => Str::limit((string) ($this->title ?? $this->name ?? $this->subject ?? $this->getKey()), 80),
            'event' => $event,
            'changes' => $changes->isNotEmpty() ? $changes->toArray() : null,
        ]);
    }

    private function summarizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return Str::limit(strip_tags($value), 120);
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return Str::limit((string) json_encode($value), 120);
    }
}
