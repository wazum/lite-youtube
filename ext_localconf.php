<?php

declare(strict_types=1);

$rendererRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::class);
$rendererRegistry->registerRendererClass(
    Wazum\LiteYoutubeRenderer\Infrastructure\Typo3\Renderer\LiteYouTubeRenderer::class
);
