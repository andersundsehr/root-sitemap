<?php

declare(strict_types=1);

namespace AUS\RootSitemap\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class RootSitemapMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/sitemap.xml') {
            return $handler->handle($request);
        }

        $site = $request->getAttribute('site');
        assert($site instanceof Site, 'This middleware needs the site attribute');
        $sitemapUrls = $this->getSitemapsPerLanguage($site);
        foreach ($sitemapUrls as $sitemapUrl) {
            if ($sitemapUrl->getHost() !== $request->getUri()->getHost()) {
                continue;
            }

            if ($sitemapUrl->getPath() !== $request->getUri()->getPath()) {
                continue;
            }

            return $handler->handle($request);
        }

        $renderHtml = $this->renderHtml($sitemapUrls);
        return (new HtmlResponse($renderHtml))
            ->withAddedHeader('Content-Type', 'application/xml;charset=utf-8')
            ->withAddedHeader('X-Robots-Tag', 'noindex');
    }

    /**
     * @return array<UriInterface>
     */
    private function getSitemapsPerLanguage(Site $site): array
    {
        $urls = [];
        $languages = $site->getAllLanguages();
        foreach ($languages as $language) {
            if (!$language->enabled()) {
                continue;
            }

            try {
                $urls[] = $site->getRouter()->generateUri($site->getRootPageId(), ['type' => '1533906435', '_language' => $language]);
            } catch (InvalidRouteArgumentsException) {
            }
        }

        $externalSitemaps = $site->getConfiguration()['external_sitemaps'];
        $base = $site->getBase();
        foreach (GeneralUtility::trimExplode("\n", $externalSitemaps, true) as $externalSitemap) {
            $uri = new Uri($externalSitemap);
            if (!$uri->getScheme()) {
                $uri = $uri->withScheme($base->getScheme());
            }

            if (!$uri->getHost()) {
                $uri = $uri->withHost($base->getHost());
            }

            if (!$uri->getPort()) {
                $uri = $uri->withPort($base->getPort());
            }

            $urls[] = $uri;
        }

        return $urls;
    }

    /**
     * @param array<UriInterface> $urls
     */
    private function renderHtml(array $urls): string
    {
        $locs = [];
        foreach ($urls as $url) {
            $locs[] = '    <sitemap>
        <loc>' . htmlentities((string)$url) . '</loc>
    </sitemap>';
        }

        $xsl = PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName('EXT:seo/Resources/Public/CSS/Sitemap.xsl'));

        return '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="' . $xsl . '"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
' . implode("\n", $locs) . '
</sitemapindex>
';
    }
}
