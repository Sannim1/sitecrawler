<?php

namespace SiteCrawler;

use SiteCrawler\Factories\DOMFactory;
use SiteCrawler\Factories\UrlFactory;
use SiteCrawler\Url;
use Symfony\Component\DomCrawler\Crawler as DOMCrawler;

class HtmlParser
{
    /**
     * object for creating DOM crawler objects.
     *
     * @var SiteCrawler\Factories\DOMFactory
     */
    protected $domFactory;

    /**
     * object for creating URL objects
     *
     * @var SiteCrawler\Factories\UrlFactory
     */
    protected $urlFactory;

    public function __construct(DOMFactory $domFactory, UrlFactory $urlFactory)
    {
        $this->domFactory = $domFactory;
        $this->urlFactory = $urlFactory;
    }

    /**
     * makes a DOM crawler object representing the HTML page at the supplied URL
     *
     * @param  Url    $pageUrl
     * @param  string $html
     *
     * @return Symfony\Component\DomCrawler\Crawler
     */
    public function makeDocument(Url $pageUrl, $html)
    {
        return $this->domFactory->makeDOMCrawler($pageUrl, $html);
    }

    /**
     * gets the URLs of all the links present in the supplied DOM object
     *
     * @param  Symfony\Component\DomCrawler\Crawler $domCrawler
     *
     * @return array
     */
    public function getLinks(DOMCrawler $domCrawler)
    {
        $links = $domCrawler->filter("a")->links();
        array_walk($links, function (&$link) {
            $link = $this->urlFactory->makeUrl($link->getUri());
        });
        return $links;
    }

    /**
     * gets the URLs of all the images present in the supplied DOM object
     *
     * @param  Symfony\Component\DomCrawler\Crawler $domCrawler
     *
     * @return array
     */
    public function getImages(DOMCrawler $domCrawler)
    {
        $images = $domCrawler->filter("img")->images();
        array_walk($images, function (&$image) {
            $image = $this->urlFactory->makeUrl($image->getUri());
        });
        return $images;
    }

    /**
     * gets the URLs of all the icons present in the supplied DOM object
     *
     * @param  Symfony\Component\DomCrawler\Crawler $domCrawler
     *
     * @return array
     */
    public function getIcons(DOMCrawler $domCrawler)
    {
        $icons = $domCrawler->filter("link[rel=icon]")->links();
        $icons = array_merge($icons, $domCrawler->filter("link[rel=apple-touch-icon]")->links());
        $icons = array_merge($icons, $domCrawler->filter("link[rel=apple-touch-startup-image]")->links());

        array_walk($icons, function (&$icon) {
            $icon = $this->urlFactory->makeUrl($icon->getUri());
        });

        return $icons;
    }

    /**
     * gets the URLs of all the stylesheets present in the supplied DOM object
     *
     * @param  Symfony\Component\DomCrawler\Crawler $domCrawler
     *
     * @return array
     */
    public function getStyleSheets(DOMCrawler $domCrawler)
    {
        $styleSheets = $domCrawler->filter("link[rel=stylesheet]")->links();
        array_walk($styleSheets, function (&$sheet) {
            $sheet = $this->urlFactory->makeUrl($sheet->getUri());
        });
        return $styleSheets;
    }

    /**
     * gets the URLs of all the scripts present in the supplied DOM object
     *
     * @param  Symfony\Component\DomCrawler\Crawler $domCrawler
     *
     * @return array
     */
    public function getScripts(DOMCrawler $domCrawler)
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

    /**
     * gets the URLs of all the assets(images, icons, stylesheets and scripts) present in the supplied DOM object
     *
     * @param  Symfony\Component\DomCrawler\Crawler $domCrawler
     *
     * @return array
     */
    public function getAssets(DOMCrawler $domCrawler)
    {
        return array_merge(
            $this->getImages($domCrawler),
            $this->getIcons($domCrawler),
            $this->getStyleSheets($domCrawler),
            $this->getScripts($domCrawler)
        );
    }
}
