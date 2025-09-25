<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Tests\Unit\Application\Mapper;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wazum\LiteYoutubeRenderer\Application\Mapper\LiteYoutubeOptionsMapper;

final class LiteYoutubeOptionsMapperTest extends TestCase
{
    #[Test]
    public function mapsDefaultsWithoutProps(): void
    {
        $options = LiteYoutubeOptionsMapper::map([], []);

        $attrs = $options->toAttributes();
        self::assertSame('lazy', $attrs['posterloading']);
        // Default
        self::assertTrue(isset($attrs['nocookie']));
        self::assertArrayNotHasKey('videoStartAt', $attrs);
        self::assertSame('Play', $attrs['videoPlay']);
    }

    #[Test]
    public function mapsReferenceBooleansAndParams(): void
    {
        $ref = [
            'title' => 'My Title',
            'tx_liteyoutuberenderer_autoload' => 1,
            'tx_liteyoutuberenderer_no_cookie' => 1,
            'tx_liteyoutuberenderer_short' => 0,
            'tx_liteyoutuberenderer_show_title' => 1,
            'tx_liteyoutuberenderer_poster_loading' => 'eager',
            'tx_liteyoutuberenderer_playlist_id' => 'PL123',
            'tx_liteyoutuberenderer_video_start_at' => 42,
            'tx_liteyoutuberenderer_param_controls' => 1,
            'tx_liteyoutuberenderer_param_rel' => 0,
            'tx_liteyoutuberenderer_param_loop' => 0,
            'tx_liteyoutuberenderer_param_mute' => 1,
        ];

        $options = LiteYoutubeOptionsMapper::map($ref, []);
        $attrs = $options->toAttributes();

        self::assertTrue(isset($attrs['autoload']));
        self::assertTrue(isset($attrs['nocookie']));
        self::assertSame('eager', $attrs['posterloading']);
        self::assertSame('Play', $attrs['videoPlay']);
        self::assertSame('My Title', $attrs['videotitle']);
        self::assertSame('PL123', $attrs['playlistid']);
        self::assertSame('42', $attrs['videoStartAt']);
        \parse_str($attrs['params'], $params);
        self::assertSame([
            'controls' => '1',
            'rel' => '0',
            'loop' => '0',
            'mute' => '1',
        ], $params);
    }

    #[Test]
    public function renderOptionsOverrideReference(): void
    {
        $ref = [
            'tx_liteyoutuberenderer_param_controls' => 0,
        ];
        $opts = ['controls' => '1'];
        $options = LiteYoutubeOptionsMapper::map($ref, $opts);
        $attrs = $options->toAttributes();
        \parse_str($attrs['params'], $params);

        self::assertSame('1', $params['controls']);
    }

    #[Test]
    public function showTitleToggleControlsVideoTitle(): void
    {
        // When show_title is disabled, videotitle should be empty
        $ref = [
            'title' => 'My Video Title',
            'tx_liteyoutuberenderer_show_title' => 0,
        ];
        $options = LiteYoutubeOptionsMapper::map($ref, []);
        $attrs = $options->toAttributes();

        self::assertArrayNotHasKey('videotitle', $attrs);

        // When show_title is enabled, videotitle should be set
        $ref['tx_liteyoutuberenderer_show_title'] = 1;
        $options = LiteYoutubeOptionsMapper::map($ref, []);
        $attrs = $options->toAttributes();

        self::assertSame('My Video Title', $attrs['videotitle']);
    }

    #[Test]
    public function zeroStartTimeIsRespected(): void
    {
        $ref = [
            'tx_liteyoutuberenderer_video_start_at' => 0,
        ];

        $options = LiteYoutubeOptionsMapper::map($ref, []);
        $attrs = $options->toAttributes();

        self::assertSame('0', $attrs['videoStartAt']);
    }

    #[Test]
    public function renderOptionsBooleanParamsNormalizeToZeroOneStrings(): void
    {
        $ref = [];
        $opts = [
            'controls' => true,
            'rel' => false,
        ];

        $options = LiteYoutubeOptionsMapper::map($ref, $opts);
        $attrs = $options->toAttributes();
        \parse_str($attrs['params'], $params);

        self::assertSame('1', $params['controls']);
        self::assertSame('0', $params['rel']);
    }

    #[Test]
    public function customPlayLabelIsUsed(): void
    {
        $ref = [];
        $opts = [];

        $options = LiteYoutubeOptionsMapper::map($ref, $opts, 'Abspielen');
        $attrs = $options->toAttributes();

        self::assertSame('Abspielen', $attrs['videoPlay']);
    }
}
