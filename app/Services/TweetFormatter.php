<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Tweet;
use App\Models\User;
use App\Notifications\MentionNotification;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class TweetFormatter
{
    protected $converter;

    public function __construct()
    {
        // Create markdown environment
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
            'renderer' => [
                'soft_break' => "<br>",
            ],
        ]);

        // Add the CommonMark core extension
        $environment->addExtension(new CommonMarkCoreExtension());

        // Add the autolink extension
        $environment->addExtension(new AutolinkExtension());

        // Create the converter
        $this->converter = new MarkdownConverter($environment);
    }

    public function format(string $text): string
    {
        $text = preg_replace_callback(
            '/(^|[^\\\\])([-#*_`~\{}|>!+])/m',
            function ($matches) {
                return $matches[1] . '\\' . $matches[2];
            },
            $text
        );
        // Preserve line breaks before Markdown conversion
        $text = nl2br(htmlspecialchars($text), false);

        // Convert text with CommonMark
        $html = $this->converter->convert($text)->getContent();

        // Add styles to all links
        $html = preg_replace(
            '/<a href="(.*?)"(.*?)>(.*?)<\/a>/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline"$2>$3</a>',
            $html
        );

        // Add RTL support for Arabic text
        $html = '<div class="whitespace-pre-wrap arabic-text arabic-container text-right" dir="rtl">' . $html . '</div>';

        return $html;
    }

    public function formatWithMentions(string $text): string
    {
        // Handle hashtags first to preserve Arabic text
        $appUrl = config('app.url');
        $text = preg_replace('/(?<=^|[^\w])#([\w\p{Arabic}]+)/u', '[$0](' . $appUrl . '/hashtag/$1)', $text);

        // Handle @mentions - convert to links
        $text = preg_replace('/(?<=^|[^\w])@([\w]{1,30})/', '[$0](' . $appUrl . '/profiles/$1)', $text);

        // Format with CommonMark
        $html = $this->format($text);

        // Style mentions and hashtags while preserving Arabic text
        $html = preg_replace(
            '/<a href="[^"]+' . '\/profiles\/(.*?)"([^>]*)>@(.*?)<\/a>/',
            '<a href="' . $appUrl . '/profiles/$1"$2 class="text-blue-500 font-medium">@$3</a>',
            $html
        );

        $html = preg_replace(
            '/<a href="[^"]+' . '\/hashtag\/(.*?)"([^>]*)>#(.*?)<\/a>/',
            '<a href="' . $appUrl . '/hashtag/$1"$2 class="text-blue-500 font-medium">#$3</a>',
            $html
        );

        return $html;
    }
}
