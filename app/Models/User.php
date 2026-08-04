<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'avatar', 'deleted_data_at', 'is_comment_banned'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use Billable, HasFactory, HasPushSubscriptions, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_data_at' => 'datetime',
            'is_comment_banned' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['admin', 'moderateur']);
    }

    public function favoriteCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function isAnonymized(): bool
    {
        return $this->deleted_data_at !== null;
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if (blank($this->avatar)) {
                return null;
            }

            return str_starts_with($this->avatar, 'http')
                ? $this->avatar
                : asset('storage/'.$this->avatar);
        });
    }
}
