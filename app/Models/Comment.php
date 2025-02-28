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
            get: function ($value) {
                // Convert URLs to clickable links with proper escaping
                $value = nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));

                // Match URLs (http, https)
                $urlPattern = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';

                // Replace URLs with anchor tags
                $value = preg_replace($urlPattern, '<a href="$0" target="_blank" rel="noopener noreferrer" class="hover:text-blue-500 underline">$0</a>', $value);

                // Return with <br> tags preserved
                return $value;
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
