<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Tests\Unit\Application\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wazum\LiteYoutubeRenderer\Application\Service\VideoRenderingService;
use Wazum\LiteYoutubeRenderer\Domain\Model\LiteYoutubeOptions;

final class VideoRenderingServiceTest extends TestCase
{
    private VideoRenderingService $service;

    protected function setUp(): void
    {
        $this->service = new VideoRenderingService();
    }

    #[Test]
    public function rendersBasicLiteYoutubeElement(): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $options = LiteYoutubeOptions::createWithDefaults();

        $html = $this->service->render($videoId, $options);

        self::assertStringContainsString('<lite-youtube', $html);
        self::assertStringContainsString('videoid="dQw4w9WgXcQ"', $html);
        self::assertStringContainsString('posterloading="lazy"', $html);
        self::assertStringContainsString('</lite-youtube>', $html);
        self::assertStringContainsString(
            '<a class="lite-youtube-fallback" href="https://www.youtube-nocookie.com/watch?v=dQw4w9WgXcQ">' .
                'Watch on YouTube</a>',
            $html
        );
    }

    #[Test]
    public function rendersWithBooleanAttributes(): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $options = LiteYoutubeOptions::fromArray([
            'autoLoad' => true,
            'noCookie' => true,
        ]);

        $html = $this->service->render($videoId, $options);

        self::assertStringContainsString('autoload', $html);
        self::assertStringContainsString('nocookie', $html);
        self::assertStringNotContainsString('autopause', $html);
    }

    #[Test]
    public function rendersWithTitleAndParams(): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $options = LiteYoutubeOptions::fromArray([
            'videoTitle' => 'Test Video',
            'videoStartAt' => 30,
            'params' => ['rel' => '0', 'controls' => '1'],
        ]);

        $html = $this->service->render($videoId, $options);

        self::assertStringContainsString('videotitle="Test Video"', $html);
        self::assertStringContainsString('videoStartAt="30"', $html);
        self::assertStringContainsString('params="rel=0&amp;controls=1"', $html);
    }

    #[Test]
    public function escapesAttributes(): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $options = LiteYoutubeOptions::fromArray([
            'videoTitle' => 'Quote" <Play>',
            'params' => ['rel' => '0'],
        ]);

        $html = $this->service->render($videoId, $options);

        self::assertStringContainsString('videotitle="Quote&quot; &lt;Play&gt;"', $html);
        self::assertStringContainsString('params="rel=0"', $html);
    }
}
