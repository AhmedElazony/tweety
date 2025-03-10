<?php

namespace App\Models;

use App\Services\TweetFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use MongoDB\BSON\Timestamp;

class Tweet extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function body(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return app(TweetFormatter::class)->formatWithMentions($value);
            },
            set: function (string $value) {
                // Clean input but preserve line breaks
                return strip_tags($value);
            },
        );
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function scopeWithLikes(Builder $query): void
    {
        // left join sub-query.
        // like: SELECT * FROM tweets
        // LEFT JOIN (SELECT tweet_id, SUM(liked = TRUE) AS likes, SUM(liked = false) AS dislikes FROM likes GROUP BY tweet_id) AS likes
        // ON likes.tweet_id = tweets.id;
        $query->leftJoinSub(
            'SELECT tweet_id, SUM(liked = TRUE) AS likes, SUM(liked = FALSE) AS dislikes FROM likes GROUP BY tweet_id',
            'likes',
            'likes.tweet_id',
            '=',
            'tweets.id'
        );
    }
    public function isShared(): bool
    {
        return (bool) $this->shares()
            ->where('tweet_id', '=', $this->id)
            ->count();
    }

    public function sharedAt(): string
    {
        return $this->shares()->createdAt();
    }

    public function scopeWithUsersSharing(Builder $query)
    {
        $query->join('shares', 'shares.tweet_id', '=', 'tweets.id')
            ->select(['tweets.*', 'likes', 'dislikes', 'shares.user_id as sharing_user']);
    }
}
