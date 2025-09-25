# TYPO3 Lite YouTube Renderer

[![CI](https://github.com/wazum/lite-youtube/actions/workflows/ci.yml/badge.svg)](https://github.com/wazum/lite-youtube/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2%20|%208.3-blue.svg)](https://www.php.net/)
[![TYPO3](https://img.shields.io/badge/TYPO3-12.4%20|%2013.4-orange.svg)](https://typo3.org/)
[![License](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](LICENSE)

High-performance YouTube video rendering using the `lite-youtube` web component for TYPO3.

## What This Extension Does

This extension replaces standard YouTube iframe embeds with `<lite-youtube>` custom elements in TYPO3. It **only** handles the server-side rendering - you need to provide the [lite-youtube web component](https://github.com/justinribeiro/lite-youtube) (a self-contained, dependency-free JavaScript module) yourself.

## Benefits

- **224× faster loading** - Shows thumbnail until clicked instead of loading 1.3MB iframe
- **Privacy-focused** - No cookies until user interaction
- **GDPR compliant** - Uses youtube-nocookie.com when configured
- **Zero vendor lock-in** - Use any version of lite-youtube you want

## Installation

```bash
composer require wazum/lite-youtube-renderer
```

Clear TYPO3 caches after installation.

> ⚠️ **Required CSP Headers**
> If using Content Security Policy, you MUST add:
> - `frame-src`: `youtube.com` `youtube-nocookie.com`
> - `img-src`: `i.ytimg.com`
>
> Without these, the video thumbnails won't load and playback will be blocked!

## Required: Web Component Setup

The extension renders `<lite-youtube>` elements. These need the lite-youtube JavaScript to work. The component is **self-contained with no dependencies** - just one JavaScript module that includes everything (including styles).

### NPM Package (Recommended)

```bash
npm install @justinribeiro/lite-youtube
```

In your JavaScript bundle:
```javascript
import '@justinribeiro/lite-youtube'
```

See [lite-youtube documentation](https://github.com/justinribeiro/lite-youtube) for more options.

## Configuration

### File Reference Options

When editing YouTube videos in TYPO3, configure:

- **Title** - The video title (used as aria-label when Show Title is enabled)
- **Show Title** - Display the title attribute on the video element
- **Autoload** - Load player automatically on view (not autoplay)
- **No Cookie** - Use youtube-nocookie.com (enabled by default for privacy)
- **YouTube Shorts** - Enable 9:16 aspect ratio for Shorts format
- **Poster Loading** - Lazy or eager loading for thumbnail
- **Start At** - Start time in seconds
- **Playlist ID** - YouTube playlist to play
- **Player Parameters** - Controls, related videos, loop, mute

The play button label is automatically localized based on the site's language.

## Styling

Customize the play button and player appearance with CSS:

```css
/* Change play button color (default is red) */
lite-youtube::part(playButton) {
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 68 48"><path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55c-2.93.78-4.63 3.26-5.42 6.19C.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="%2305aa5a"/><path d="M45 24 27 14v20" fill="white"/></svg>');
}

/* Adjust aspect ratio */
lite-youtube {
  --lite-youtube-aspect-ratio: 16 / 9;  /* default */
}
```

See [lite-youtube documentation](https://github.com/justinribeiro/lite-youtube#styling) for more styling options and CSS custom properties.

You can also switch the aspect ratio for YouTube Shorts with CSS when the `short` attribute is set. SCSS example:

```css
lite-youtube[short] {
    aspect-ratio: 9 / 16;

    // Make the preview image cover the container for shorts
    img[slot="image"] {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    @media screen and (min-width: 768px) {
        aspect-ratio: 16 / 9;

        img[slot="image"] {
            object-fit: contain;
        }
    }
}
```

(YouTube does not provide separate thumbnails for Shorts, so this is kind of a workaround)

## How It Works

1. TYPO3 detects YouTube video files (mime type: video/youtube)
2. Instead of rendering an iframe, outputs: `<lite-youtube videoid="..." nocookie ...></lite-youtube>`
3. Thumbnails are cached locally using TYPO3's media storage
4. Your included lite-youtube JavaScript converts this to an interactive thumbnail
5. On click, loads the actual YouTube iframe

## Browser Support

Requires browser Web Components support (should be the new normal).

## License

GNU General Public License version 2 or later (GPL-2.0-or-later) - see [LICENSE](LICENSE)

## Credits

- [lite-youtube](https://github.com/justinribeiro/lite-youtube) by Justin Ribeiro
- TYPO3 integration by [Wolfgang Klinger](https://wolfgang-klinger.dev)
