<?php

/** @var string $_EXTKEY */
$EM_CONF[$_EXTKEY] = [
    'title' => 'root_sitemap',
    'description' => '/sitemap.xml will show all language sitemaps',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-13.4.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'AUS\\RootSitemap\\' => 'Classes/',
        ],
    ],
];
