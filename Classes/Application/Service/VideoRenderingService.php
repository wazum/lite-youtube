<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Application\Service;

use Wazum\LiteYoutubeRenderer\Domain\Model\LiteYoutubeOptions;

final readonly class VideoRenderingService
{
    public function render(string $videoId, LiteYoutubeOptions $options, ?string $posterImageUrl = null): string
    {
        $htmlAttributes = [];
        $htmlAttributes[] = \sprintf('videoid="%s"', $this->escapeAttribute($videoId));

        $optionAttributes = $options->toAttributes();
        foreach ($optionAttributes as $key => $value) {
            if (true === $value) {
                $htmlAttributes[] = $key;
            } else {
                $htmlAttributes[] = \sprintf('%s="%s"', $key, $this->escapeAttribute((string) $value));
            }
        }

        $innerContent = '';

        if ($posterImageUrl !== null) {
            $innerContent .= \sprintf(
                '<img slot="image" src="%s" alt="" loading="lazy" />',
                $this->escapeAttribute($posterImageUrl)
            );
        }

        $domain = $options->noCookie ? 'www.youtube-nocookie.com' : 'www.youtube.com';
        $youtubeUrl = \sprintf('https://%s/watch?v=%s', $domain, $this->escapeAttribute($videoId));
        $fallbackLink = \sprintf(
            '<a class="lite-youtube-fallback" href="%s">Watch on YouTube</a>',
            $youtubeUrl
        );
        $innerContent .= $fallbackLink;

        return \sprintf(
            '<lite-youtube %s>%s</lite-youtube>',
            \implode(' ', $htmlAttributes),
            $innerContent
        );
    }

    private function escapeAttribute(string $value): string
    {
        return \htmlspecialchars($value, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
    }
}
