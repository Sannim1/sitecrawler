<?php

namespace SiteCrawler;

use SiteCrawler\Factories\DOMFactory;
use SiteCrawler\Factories\UrlFactory;

class HtmlParser
{
    protected $domFactory;
    protected $urlFactory;

    public function __construct(DOMFactory $domFactory, UrlFactory $urlFactory)
    {
        $this->domFactory = $domFactory;
        $this->urlFactory = $urlFactory;
    }

    public function makeDocument($html)
    {
        $dom = $this->domFactory->makeDOM();

        $dom->loadHTML($html);

        return $dom;
    }

    public function getLinks(\DOMDocument $document)
    {
        $links = [];
        $anchorTags = $document->getElementsByTagName('a');
        foreach ($anchorTags as $anchorTag) {
            $url = $anchorTag->getAttribute('href');
            $links[] = $this->urlFactory->makeUrl($url);
        }

        return $links;
    }

    public function getAssets(\DOMDocument $document)
    {
        $assets = [];
        $linkTags = $document->getElementsByTagName('link');
        foreach ($linkTags as $linkTag) {
            $url = $linkTag->getAttribute('href');
            $assets[] = $this->urlFactory->makeUrl($url);
        }

        $imageTags = $document->getElementsByTagName('img');
        foreach ($imageTags as $imageTag) {
            $url = $imageTag->getAttribute('src');
            $assets[] = $this->urlFactory->makeUrl($url);
        }

        $scriptTags = $document->getElementsByTagName('src');
        foreach ($scriptTags as $scriptTag) {
            $url = $scriptTag->getAttribute('href');
            $assets[] = $this->urlFactory->makeUrl($url);
        }

        return $assets;
    }
}
