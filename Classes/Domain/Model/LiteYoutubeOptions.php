<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Domain\Model;

final readonly class LiteYoutubeOptions
{
    public function __construct(
        public bool $autoLoad = false,
        public bool $noCookie = true,
        public bool $short = false,
        public PosterLoading $posterLoading = PosterLoading::LAZY,
        public string $videoTitle = '',
        public string $videoPlay = 'Play',
        public ?string $playlistId = null,
        public ?int $videoStartAt = null,
        public array $params = [],
    ) {
    }

    public static function createWithDefaults(): self
    {
        return new self();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            autoLoad: $data['autoLoad'] ?? false,
            noCookie: $data['noCookie'] ?? true,
            short: $data['short'] ?? false,
            posterLoading: isset($data['posterLoading'])
                ? match ($data['posterLoading']) {
                    'eager' => PosterLoading::EAGER,
                    default => PosterLoading::LAZY,
                }
            : PosterLoading::default(),
            videoTitle: $data['videoTitle'] ?? '',
            videoPlay: $data['videoPlay'] ?? 'Play',
            playlistId: $data['playlistId'] ?? null,
            videoStartAt: isset($data['videoStartAt']) ? (int) $data['videoStartAt'] : null,
            params: $data['params'] ?? []
        );
    }

    public function toAttributes(): array
    {
        $attributes = [];

        if ($this->autoLoad) {
            $attributes['autoload'] = true;
        }
        if ($this->noCookie) {
            $attributes['nocookie'] = true;
        }
        if ($this->short) {
            $attributes['short'] = true;
        }

        $attributes['posterloading'] = $this->posterLoading->value;

        if (!empty($this->videoTitle)) {
            $attributes['videotitle'] = $this->videoTitle;
        }
        if (!empty($this->videoPlay)) {
            $attributes['videoPlay'] = $this->videoPlay;
        }
        if (null !== $this->playlistId) {
            $attributes['playlistid'] = $this->playlistId;
        }
        if (null !== $this->videoStartAt) {
            $attributes['videoStartAt'] = (string) $this->videoStartAt;
        }

        if (!empty($this->params)) {
            $attributes['params'] = \http_build_query($this->params);
        }

        return $attributes;
    }
}
