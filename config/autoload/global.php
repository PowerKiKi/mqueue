<?php

declare(strict_types=1);

return [
    'minimize' => true,
    'googleAnalyticsTrackingCode' => '',
    'apiKey' => '',
    'templates' => [
        'paths' => [
            'app' => ['templates/app'],
            'error' => ['templates/error'],
            'layout' => ['templates/layout'],
        ],
        'map' => [
            'pagination' => 'templates/partial/pagination.phtml',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => 'data/i18n',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => ('Activity'),
                'route' => 'activity.index',

            ],
            [
                'label' => ('Movies'),
                'route' => 'movie.index',
                'pages' => [
                    [
                        'route' => 'movie.view',
                        'visible' => false,
                    ],
                ],
            ],
            [
                'label' => ('Add movie'),
                'route' => 'movie.add',

            ],
            [
                'label' => ('Users'),
                'route' => 'user.index',
                'pages' => [
                    [
                        'route' => 'user.view',
                        'visible' => false,
                    ],
                    [
                        'route' => 'user.logout',
                        'visible' => false,
                    ],
                    [
                        'route' => 'user.login',
                        'visible' => false,
                    ],
                    [
                        'route' => 'user.new',
                        'visible' => false,
                    ],
                ],
            ],
            [
                'label' => ('FAQ'),
                'route' => 'faq',
            ],
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'headScript' => Application\View\Helper\HeadScript::class,
            'languageSelector' => Application\View\Helper\LanguageSelector::class,
            'loginState' => Application\View\Helper\LoginState::class,
            'flashMessenger' => Application\View\Helper\FlashMessenger::class,
            'footer' => Application\View\Helper\Footer::class,
            'activity' => Application\View\Helper\Activity::class,
            'alternateFormats' => Application\View\Helper\AlternateFormats::class,
            'urlParams' => Application\View\Helper\UrlParams::class,
            'gravatar' => Application\View\Helper\Gravatar::class,
            'graph' => Application\View\Helper\Graph::class,
            'statusLinks' => Application\View\Helper\StatusLinks::class,
            'statusHelp' => Application\View\Helper\StatusHelp::class,
            'movie' => Application\View\Helper\Movie::class,
            'sort' => Application\View\Helper\Sort::class,
        ],
        'invokables' => [
            'link' => Application\View\Helper\Link::class,
            'rating' => Application\View\Helper\Rating::class,
        ],
        'factories' => [
            'googleAnalytics' => Application\View\Helper\GoogleAnalyticsFactory::class,
            'cacheStamp' => Application\View\Helper\CacheStampFactory::class,

            Application\View\Helper\Activity::class => Application\View\Helper\ActivityFactory::class,
            Application\View\Helper\AlternateFormats::class => Application\View\Helper\AlternateFormatsFactory::class,
            Application\View\Helper\CacheStamp::class => Application\View\Helper\CacheStampFactory::class,
            Application\View\Helper\FlashMessenger::class => Application\View\Helper\FlashMessengerFactory::class,
            Application\View\Helper\Footer::class => Application\View\Helper\FooterFactory::class,
            Application\View\Helper\GoogleAnalytics::class => Application\View\Helper\GoogleAnalyticsFactory::class,
            Application\View\Helper\Graph::class => Application\View\Helper\GraphFactory::class,
            Application\View\Helper\Gravatar::class => Application\View\Helper\GravatarFactory::class,
            Application\View\Helper\HeadLink::class => Application\View\Helper\HeadLinkFactory::class,
            Application\View\Helper\HeadScript::class => Application\View\Helper\HeadScriptFactory::class,
            Application\View\Helper\LanguageSelector::class => Application\View\Helper\LanguageSelectorFactory::class,
            Application\View\Helper\LoginState::class => Application\View\Helper\LoginStateFactory::class,
            Application\View\Helper\Movie::class => Application\View\Helper\MovieFactory::class,
            Application\View\Helper\Sort::class => Application\View\Helper\SortFactory::class,
            Application\View\Helper\StatusHelp::class => Application\View\Helper\StatusHelpFactory::class,
            Application\View\Helper\StatusLinks::class => Application\View\Helper\StatusLinksFactory::class,
            Application\View\Helper\UrlParams::class => Application\View\Helper\UrlParamsFactory::class,
        ],
    ],
];
