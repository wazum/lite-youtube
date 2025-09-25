<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Infrastructure\Typo3\Renderer;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\PathUtility;
use Wazum\LiteYoutubeRenderer\Application\Mapper\LiteYoutubeOptionsMapper;
use Wazum\LiteYoutubeRenderer\Application\Service\VideoRenderingService;
use Wazum\LiteYoutubeRenderer\Domain\Model\LiteYoutubeOptions;

final class LiteYouTubeRenderer implements FileRendererInterface
{
    private const PRIORITY = 100;

    public function __construct(
        private readonly VideoRenderingService $renderingService,
        private readonly OnlineMediaHelperRegistry $onlineMediaHelperRegistry,
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {
    }

    public function getPriority(): int
    {
        return self::PRIORITY;
    }

    public function canRender(FileInterface $file): bool
    {
        $isYoutubeType = ('video/youtube' === $file->getMimeType() || 'youtube' === $file->getExtension());
        if (!$isYoutubeType) {
            return false;
        }

        return null !== $this->resolveVideoId($file);
    }

    public function render(FileInterface $file, $width, $height, array $options = []): string
    {
        $videoId = $this->resolveVideoId($file);
        if (null === $videoId || '' === $videoId) {
            return '';
        }

        $localizedPlayLabel = $this->getLocalizedPlayLabel();
        $liteOptions = $this->createOptionsFromFile($file, $options, $localizedPlayLabel);
        $posterImageUrl = $this->getLocalPosterImageUrl($file);

        return $this->renderingService->render($videoId, $liteOptions, $posterImageUrl);
    }

    private function resolveVideoId(FileInterface $file): ?string
    {
        $originalFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;
        if (!$originalFile instanceof File) {
            return null;
        }
        $mediaHelper = $this->onlineMediaHelperRegistry->getOnlineMediaHelper($originalFile);
        if (!$mediaHelper instanceof YouTubeHelper) {
            return null;
        }

        return $mediaHelper->getOnlineMediaId($originalFile);
    }

    private function createOptionsFromFile(FileInterface $file, array $options, string $playLabel): LiteYoutubeOptions
    {
        $referenceProperties = [];

        if (!($file instanceof FileReference)) {
            return LiteYoutubeOptionsMapper::map($referenceProperties, $options, $playLabel);
        }

        $referenceProperties = $file->getProperties();

        return LiteYoutubeOptionsMapper::map($referenceProperties, $options, $playLabel);
    }

    private function getLocalizedPlayLabel(): string
    {
        $request = $this->getCurrentRequest();
        if (!$request instanceof ServerRequestInterface) {
            return 'Play';
        }

        $language = $request->getAttribute('language');
        if (!$language instanceof SiteLanguage) {
            return 'Play';
        }

        $languageService = $this->languageServiceFactory->createFromSiteLanguage($language);
        $label = $languageService->sL(
            'LLL:EXT:lite_youtube/Resources/Private/Language/locallang.xlf:video.play'
        );

        return $label ?: 'Play';
    }

    private function getLocalPosterImageUrl(FileInterface $file): ?string
    {
        $originalFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;
        if (!$originalFile instanceof File) {
            return null;
        }

        $mediaHelper = $this->onlineMediaHelperRegistry->getOnlineMediaHelper($originalFile);
        if (!$mediaHelper instanceof YouTubeHelper) {
            return null;
        }

        $previewImagePath = $mediaHelper->getPreviewImage($originalFile);
        if (!\file_exists($previewImagePath)) {
            return null;
        }

        return PathUtility::getAbsoluteWebPath($previewImagePath);
    }

    private function getCurrentRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
