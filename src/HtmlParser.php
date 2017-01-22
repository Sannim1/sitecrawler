<?php

namespace SiteCrawler;

use SiteCrawler\Factories\DOMFactory;
use SiteCrawler\Factories\UrlFactory;
use SiteCrawler\Url;
use Symfony\Component\DomCrawler\Crawler;

class HtmlParser
{
    protected $domFactory;
    protected $urlFactory;

    public function __construct(DOMFactory $domFactory, UrlFactory $urlFactory)
    {
        $this->domFactory = $domFactory;
        $this->urlFactory = $urlFactory;
    }

    public function makeDocument(Url $pageUrl, $html)
    {
        return $this->domFactory->makeDOMCrawler($pageUrl, $html);
    }

    public function getLinks(Crawler $domCrawler)
    {
        $links = $domCrawler->filter("a")->links();
        array_walk($links, function (&$link) {
            $link = $this->urlFactory->makeUrl($link->getUri());
        });
        return $links;
    }

    public function getImages(Crawler $domCrawler)
    {
        $images = $domCrawler->filter("img")->images();
        array_walk($images, function (&$image) {
            $image = $this->urlFactory->makeUrl($image->getUri());
        });
        return $images;
    }

    public function getIcons(Crawler $domCrawler)
    {
        $icons = $domCrawler->filter("link[rel=icon]")->links();
        $icons = array_merge($icons, $domCrawler->filter("link[rel=apple-touch-icon]")->links());
        $icons = array_merge($icons, $domCrawler->filter("link[rel=apple-touch-startup-image]")->links());

        array_walk($icons, function (&$icon) {
            $icon = $this->urlFactory->makeUrl($icon->getUri());
        });

        return $icons;
    }

    public function getStyleSheets(Crawler $domCrawler)
    {
        $styleSheets = $domCrawler->filter("link[rel=stylesheet]")->links();
        array_walk($styleSheets, function (&$sheet) {
            $sheet = $this->urlFactory->makeUrl($sheet->getUri());
        });
        return $styleSheets;
    }

    public function getScripts(Crawler $domCrawler)
    {
        $scripts = [];
        $scriptTags = $domCrawler->filter("script");
        $baseUrl = $this->urlFactory->makeUrl($scriptTags->getUri());
        foreach ($scriptTags as $scriptTag) {
            $scriptSource = $scriptTag->getAttribute('src');
            if ($scriptSource == "") {
                continue;
            }
            $scripts[] = $this->urlFactory->makeAssetUrl($baseUrl, $scriptSource);
        }

        return $scripts;
    }

    public function getAssets(Crawler $domCrawler)
    {
        return array_merge(
            $this->getImages($domCrawler),
            $this->getIcons($domCrawler),
            $this->getStyleSheets($domCrawler),
            $this->getScripts($domCrawler)
        );
    }

    // private function makeUrl(Url $parentUrl, $url)
    // {
    //     $scheme = parse_url($url, PHP_URL_SCHEME);
    //     if (isset($scheme)) {
    //         return $this->urlFactory->makeUrl($url);
    //     }

    //     if (strpos($url, "//") === 0) {
    //         $scheme = $parentUrl->getScheme();
    //         return $this->urlFactory->makeUrl("{$scheme}:{$url}");
    //     }

    //     return $this->urlFactory->makeUrlFromPath($parentUrl, $url);
    // }
}
