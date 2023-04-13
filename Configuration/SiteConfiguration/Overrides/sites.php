<?php

// Experimental example to add a new field to the site configuration

// Configure a new simple required input field to site
$GLOBALS['SiteConfiguration']['site']['columns']['external_sitemaps'] = [
    'label' => 'Additional Sitemaps',
    'description' => 'add one external Sitemap per line (starting with /.. or with full https://domain...',
    'config' => [
        'type' => 'text',
    ],
];


// And add it to showitem
$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base, external_sitemaps, ',
    (string) $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem']
);
