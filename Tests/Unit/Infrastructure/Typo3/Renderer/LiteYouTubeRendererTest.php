<?php

declare(strict_types=1);

namespace Wazum\LiteYoutubeRenderer\Tests\Unit\Infrastructure\Typo3\Renderer;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;
use Wazum\LiteYoutubeRenderer\Application\Service\VideoRenderingService;
use Wazum\LiteYoutubeRenderer\Infrastructure\Typo3\Renderer\LiteYouTubeRenderer;

final class LiteYouTubeRendererTest extends TestCase
{
    #[Test]
    public function canRenderReturnsFalseForNonYoutube(): void
    {
        $service = new VideoRenderingService();
        $registry = $this->createMock(OnlineMediaHelperRegistry::class);
        $languageServiceFactory = $this->createMock(LanguageServiceFactory::class);
        $renderer = new LiteYouTubeRenderer($service, $registry, $languageServiceFactory);

        $file = $this->createMock(FileInterface::class);
        $file->method('getMimeType')->willReturn('video/mp4');
        $file->method('getExtension')->willReturn('mp4');

        self::assertFalse($renderer->canRender($file));
    }

    #[Test]
    public function renderReturnsEmptyStringWhenNoVideoId(): void
    {
        $service = new VideoRenderingService();
        $registry = $this->createMock(OnlineMediaHelperRegistry::class);
        $languageServiceFactory = $this->createMock(LanguageServiceFactory::class);
        $renderer = new LiteYouTubeRenderer($service, $registry, $languageServiceFactory);

        $file = $this->createMock(FileReference::class);
        $originalFile = $this->createMock(File::class);
        $file->method('getOriginalFile')->willReturn($originalFile);

        $youTubeHelper = $this->createMock(YouTubeHelper::class);
        $youTubeHelper->method('getOnlineMediaId')->willReturn('');
        $registry->method('getOnlineMediaHelper')->willReturn($youTubeHelper);

        $file->method('getMimeType')->willReturn('video/youtube');
        $file->method('getExtension')->willReturn('youtube');

        self::assertSame('', $renderer->render($file, 0, 0));
    }

    #[Test]
    public function renderPassesOptionsFromReferenceAndFile(): void
    {
        $service = new VideoRenderingService();
        $registry = $this->createMock(OnlineMediaHelperRegistry::class);
        $languageServiceFactory = $this->createMock(LanguageServiceFactory::class);
        $renderer = new LiteYouTubeRenderer($service, $registry, $languageServiceFactory);

        $fileReference = $this->createMock(FileReference::class);
        $originalFile = $this->createMock(File::class);
        $fileReference->method('getOriginalFile')->willReturn($originalFile);

        $fileReference->method('getMimeType')->willReturn('video/youtube');
        $fileReference->method('getExtension')->willReturn('youtube');
        $fileReference->method('getProperties')->willReturn([
            'tx_liteyoutuberenderer_playlist_id' => 'PL999',
        ]);

        $originalFile->method('hasProperty')->willReturn(false);
        $originalFile->method('getProperty')->willReturn(null);

        $youTubeHelper = $this->createMock(YouTubeHelper::class);
        $youTubeHelper->method('getOnlineMediaId')->willReturn('abc123');
        $youTubeHelper->method('getPreviewImage')->willReturn('/path/to/thumbnail.jpg');
        $registry->method('getOnlineMediaHelper')->willReturn($youTubeHelper);

        $html = $renderer->render($fileReference, 0, 0);
        self::assertStringContainsString('videoid="abc123"', $html);
        self::assertStringContainsString('playlistid="PL999"', $html);
    }
}
