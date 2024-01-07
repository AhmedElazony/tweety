<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
            set: fn (string $value) => $this->body = nl2br($value),
        );
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function scopeWithLikes(Builder $query): void
    {
        // left join sub-query.
        // like SELECT * FROM tweets
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
}
