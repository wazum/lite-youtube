<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wazum\LiteYoutubeRenderer\Domain\Model\LiteYoutubeOptions;
use Wazum\LiteYoutubeRenderer\Domain\Model\PosterLoading;

final class LiteYoutubeOptionsTest extends TestCase
{
    #[Test]
    public function createsWithDefaults(): void
    {
        $options = LiteYoutubeOptions::createWithDefaults();

        self::assertFalse($options->autoLoad);
        self::assertTrue($options->noCookie);
        self::assertFalse($options->short);
        self::assertSame(PosterLoading::LAZY, $options->posterLoading);
        self::assertSame('Play', $options->videoPlay);
        self::assertSame('', $options->videoTitle);
        self::assertNull($options->playlistId);
        self::assertNull($options->videoStartAt);
        self::assertSame([], $options->params);
    }

    #[Test]
    public function createsFromArray(): void
    {
        $data = [
            'autoLoad' => true,
            'noCookie' => true,
            'posterLoading' => 'eager',
            'videoTitle' => 'Test Video',
            'videoStartAt' => 30,
            'params' => ['rel' => '0', 'controls' => '1'],
        ];

        $options = LiteYoutubeOptions::fromArray($data);

        self::assertTrue($options->autoLoad);
        self::assertTrue($options->noCookie);
        self::assertSame(PosterLoading::EAGER, $options->posterLoading);
        self::assertSame('Test Video', $options->videoTitle);
        self::assertSame(30, $options->videoStartAt);
        self::assertSame(['rel' => '0', 'controls' => '1'], $options->params);
    }

    #[Test]
    public function convertsToAttributes(): void
    {
        $options = LiteYoutubeOptions::fromArray([
            'autoLoad' => true,
            'noCookie' => true,
            'videoTitle' => 'My Video',
            'videoStartAt' => 45,
            'params' => ['rel' => '0', 'modestbranding' => '1'],
        ]);

        $attributes = $options->toAttributes();

        self::assertSame([
            'autoload' => true,
            'nocookie' => true,
            'posterloading' => 'lazy',
            'videotitle' => 'My Video',
            'videoPlay' => 'Play',
            'videoStartAt' => '45',
            'params' => 'rel=0&modestbranding=1',
        ], $attributes);
    }

    #[Test]
    public function omitsNullAndEmptyValues(): void
    {
        $options = LiteYoutubeOptions::createWithDefaults();

        $attributes = $options->toAttributes();

        self::assertArrayNotHasKey('playlistid', $attributes);
        self::assertArrayNotHasKey('videoStartAt', $attributes);
        self::assertArrayNotHasKey('short', $attributes);
        self::assertArrayNotHasKey('autoload', $attributes);
    }
}
