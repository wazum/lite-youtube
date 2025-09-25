<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$ll = 'LLL:EXT:lite_youtube/Resources/Private/Language/locallang_db.xlf:';

$newColumns = [
    'tx_liteyoutuberenderer_autoload' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_autoload',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
    'tx_liteyoutuberenderer_no_cookie' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_no_cookie',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 1,
        ],
    ],
    'tx_liteyoutuberenderer_short' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_short',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
    'tx_liteyoutuberenderer_show_title' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_show_title',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
    'tx_liteyoutuberenderer_poster_loading' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_poster_loading',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'default' => 'lazy',
            'items' => [
                [$ll . 'sys_file_reference.lyt_posterloading.lazy', 'lazy'],
                [$ll . 'sys_file_reference.lyt_posterloading.eager', 'eager'],
            ],
        ],
    ],
    'tx_liteyoutuberenderer_playlist_id' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_playlist_id',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
            'nullable' => true,
        ],
    ],
    'tx_liteyoutuberenderer_video_start_at' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_video_start_at',
        'config' => [
            'type' => 'number',
            'size' => 10,
            'range' => ['lower' => 0],
            'default' => 0,
        ],
    ],
    'tx_liteyoutuberenderer_param_controls' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_param_controls',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 1,
        ],
    ],
    'tx_liteyoutuberenderer_param_rel' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_param_rel',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
    'tx_liteyoutuberenderer_param_loop' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_param_loop',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
    'tx_liteyoutuberenderer_param_mute' => [
        'exclude' => true,
        'label' => $ll . 'sys_file_reference.tx_liteyoutuberenderer_param_mute',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_file_reference', $newColumns);

$GLOBALS['TCA']['sys_file_reference']['palettes']['lytYoutubePalette'] = [
    'label' => $ll . 'sys_file_reference.palette.lytYoutube',
    'showitem' => \implode(',', [
        'tx_liteyoutuberenderer_autoload, tx_liteyoutuberenderer_no_cookie, tx_liteyoutuberenderer_short,--linebreak--,',
        'tx_liteyoutuberenderer_show_title, tx_liteyoutuberenderer_poster_loading,--linebreak--,',
        'tx_liteyoutuberenderer_playlist_id, tx_liteyoutuberenderer_video_start_at,--linebreak--,',
        'tx_liteyoutuberenderer_param_controls, tx_liteyoutuberenderer_param_rel,--linebreak--,',
        'tx_liteyoutuberenderer_param_loop, tx_liteyoutuberenderer_param_mute',
    ]),
];

$GLOBALS['TCA']['sys_file_reference']['types'][FileType::VIDEO->value] = [
    'showitem' => '
            --palette--;;basicVideoOverlayPalette,
            --palette--;;lytYoutubePalette,
            --palette--;;filePalette',
];

$GLOBALS['TCA']['sys_file_reference']['palettes']['basicVideoOverlayPalette'] = [
    'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette',
    'showitem' => 'title,description,--linebreak--,autoplay',
];
