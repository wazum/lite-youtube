<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Domain\Model;

enum PosterLoading: string
{
    case LAZY = 'lazy';
    case EAGER = 'eager';

    public static function default(): self
    {
        return self::LAZY;
    }

    public function isLazy(): bool
    {
        return self::LAZY === $this;
    }
}
