<?php

use SiteCrawler\Url;
use SiteCrawler\Utils\MapMaker;

class MapMakerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_map_of_visited_urls()
    {
        $url = new Url("http://example.com");
        $mapMaker = new MapMaker($url);
        $visitedUrls = [$url];

        $cwd = getcwd();
        $assetMapFileName = "{$cwd}/sitemaps/http:example.com/assetmap.json";
        $siteMapFileName = "{$cwd}/sitemaps/http:example.com/sitemap.json";

        unlink($assetMapFileName);
        unlink($siteMapFileName);

        $this->assertFileNotExists($assetMapFileName);
        $this->assertFileNotExists($siteMapFileName);

        $mapMaker->createMap($visitedUrls);

        $this->assertFileExists($assetMapFileName);
        $this->assertFileExists($siteMapFileName);
    }
}
