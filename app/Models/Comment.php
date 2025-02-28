<?php

namespace App\Models;

use App\Services\TweetFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;
    protected $guarded = [];

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

    public function tweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
