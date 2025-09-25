<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Application\Mapper;

use Wazum\LiteYoutubeRenderer\Domain\Model\LiteYoutubeOptions;

final class LiteYoutubeOptionsMapper
{
    public static function map(
        array $referenceProps,
        array $renderOptions,
        string $playLabel = 'Play',
    ): LiteYoutubeOptions {
        $properties = self::getDefaultProperties();
        $properties['videoPlay'] = $playLabel;

        $properties = \array_merge(
            $properties,
            self::mapVideoDisplayProperties($referenceProps),
            self::mapBehaviorProperties($referenceProps),
            self::mapMediaProperties($referenceProps),
            self::mapYouTubeParameters($referenceProps)
        );

        $properties = self::applyRenderOptionsOverrides($properties, $renderOptions);

        return LiteYoutubeOptions::fromArray($properties);
    }

    private static function getDefaultProperties(): array
    {
        return [
            'videoTitle' => '',
            'params' => [],
        ];
    }

    private static function mapVideoDisplayProperties(array $referenceProps): array
    {
        $properties = [];

        if (!empty($referenceProps['tx_liteyoutuberenderer_show_title'])) {
            $properties['videoTitle'] = $referenceProps['title'] ?? $referenceProps['alternative'] ?? '';
        }

        return $properties;
    }

    private static function mapBehaviorProperties(array $referenceProps): array
    {
        return [
            'autoLoad' => !empty($referenceProps['tx_liteyoutuberenderer_autoload']),
            'noCookie' => !\array_key_exists('tx_liteyoutuberenderer_no_cookie', $referenceProps)
                || (bool) $referenceProps['tx_liteyoutuberenderer_no_cookie'],
            'short' => !empty($referenceProps['tx_liteyoutuberenderer_short']),
        ];
    }

    private static function mapMediaProperties(array $referenceProps): array
    {
        $startAt = $referenceProps['tx_liteyoutuberenderer_video_start_at'] ?? null;

        return [
            'posterLoading' => $referenceProps['tx_liteyoutuberenderer_poster_loading'] ?? null,
            'playlistId' => $referenceProps['tx_liteyoutuberenderer_playlist_id'] ?? null,
            'videoStartAt' => $startAt !== null ? (int) $startAt : null,
        ];
    }

    private static function mapYouTubeParameters(array $referenceProps): array
    {
        $allowedParams = ['controls', 'rel', 'loop', 'mute'];
        $paramMap = self::getParameterFieldMap();

        $params = [];
        foreach ($allowedParams as $param) {
            $field = $paramMap[$param];
            if (\array_key_exists($field, $referenceProps)) {
                $params[$param] = (string) ((int) (bool) $referenceProps[$field]);
            }
        }

        return ['params' => $params];
    }

    private static function getParameterFieldMap(): array
    {
        return [
            'controls' => 'tx_liteyoutuberenderer_param_controls',
            'rel' => 'tx_liteyoutuberenderer_param_rel',
            'loop' => 'tx_liteyoutuberenderer_param_loop',
            'mute' => 'tx_liteyoutuberenderer_param_mute',
        ];
    }

    private static function applyRenderOptionsOverrides(array $properties, array $renderOptions): array
    {
        $properties = \array_merge($properties, $renderOptions);

        if (!empty($renderOptions['no-cookie'])) {
            $properties['noCookie'] = true;
        }
        if (!empty($renderOptions['autoplay'])) {
            $properties['autoLoad'] = true;
        }

        $allowedParams = ['controls', 'rel', 'loop', 'mute'];
        foreach ($allowedParams as $param) {
            if (\array_key_exists($param, $renderOptions)) {
                $properties['params'][$param] = self::normalizeParamValue($renderOptions[$param]);
            }
        }

        return $properties;
    }

    private static function normalizeParamValue(mixed $value): string
    {
        if (\is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (\is_int($value)) {
            return $value !== 0 ? '1' : '0';
        }
        $string = (string) $value;
        if ($string === '') {
            return '0';
        }

        return $string;
    }
}
