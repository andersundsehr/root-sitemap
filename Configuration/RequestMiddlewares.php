<?php

use AUS\RootSitemap\Middleware\RootSitemapMiddleware;

return [
    'frontend' => [
        'a-u-s/root-sitemap/root-sitemap-middleware' => [
            'target' => RootSitemapMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
                'typo3/cms-core/normalized-params-attribute',
            ],
            'before' => [
                'typo3/cms-frontend/tsfe',
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
