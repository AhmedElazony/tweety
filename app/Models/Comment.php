<?php

namespace App\Models;

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
            set: fn (string $value) => $this->body = nl2br(strip_tags($value, ['<br>'])),
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
