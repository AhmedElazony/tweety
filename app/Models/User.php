<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as EmailVerification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements EmailVerification
{
    use HasApiTokens, HasFactory, MustVerifyEmail, Notifiable, Followable, CanLike, CanShare, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'bio',
        'avatar',
        'password',
        'dark_mode',
        'messenger_color'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value === null ? asset('images/default-avatar.jpg') : asset('storage/' . $value)
        );
    }

    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function timeline()
    {
        $followingIds = $this->following()->pluck('id');

        return Tweet::whereIn('tweets.user_id', $followingIds)
            ->orWhere('tweets.user_id', $this->id)
            ->withLikes()
            ->with('user')
            ->latest()
            ->paginate(20);
    }

    public function sharedTweetsTimeline()
    {
        $followingIds = $this->following()->pluck('id');

        return Tweet::whereIn('tweets.user_id', $followingIds)
            ->orWhere('tweets.user_id', $this->id)
            ->withLikes()
            ->with('user')
            ->withUsersSharing()
            ->latest()
            ->paginate(20);
    }

    public function path($append = ''): string
    {
        return $append ? "/profiles/{$this->username}/{$append}" : "/profiles/{$this->username}";
    }
}
