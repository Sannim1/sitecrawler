<?php

namespace SiteCrawler\Factories;

use SiteCrawler\Url;
use Symfony\Component\DomCrawler\Crawler;

class DOMFactory
{
    /**
     * Makes a DOM crawler object representing the supplied HTML string
     *
     * @param  Url    $pageUrl
     * @param  string $html
     *
     * @return Crawler
     */
    public function makeDOMCrawler(Url $pageUrl, $html)
    {
        return new Crawler($html, $pageUrl->getUrl());
    }
}
