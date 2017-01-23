<?php

namespace SiteCrawler\Utils;

use SiteCrawler\Renderers\AssetMapRenderer;
use SiteCrawler\Renderers\SiteMapRenderer;
use SiteCrawler\Url;

class MapMaker
{
    /**
     * URL for which this map is being generated
     * @var SiteCrawler\Url
     */
    protected $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * creates and persists a map from a list of visited URLs
     *
     * @param  array $visitedUrls
     *
     * @return null
     */
    public function createMap($visitedUrls)
    {
        $siteMap = (new SiteMapRenderer)->render($visitedUrls);
        $assetMap = (new AssetMapRenderer)->render($visitedUrls);

        $outputDirectory = getcwd() . "/sitemaps/" . str_replace("/", "", $this->url->getUrl());
        if (! is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        $siteMapFileName = "{$outputDirectory}/sitemap.json";
        $assetMapFileName = "{$outputDirectory}/assetmap.json";

        file_put_contents($siteMapFileName, json_encode($siteMap));
        file_put_contents($assetMapFileName, json_encode($assetMap));

        echo "Sitemap written to: {$siteMapFileName}" . PHP_EOL;
        echo "Asset map written to: {$assetMapFileName}" . PHP_EOL;
    }
}
