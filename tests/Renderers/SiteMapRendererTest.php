<?php

use SiteCrawler\Renderers\SiteMapRenderer;
use SiteCrawler\Url;

class SiteMapRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_generates_a_sitemap_from_an_array_of_visited_urls()
    {
        $url = new Url("http://example.com");
        $url->addLink(new Url("http://iana.org"));
        $url->addAsset(new Url("http://example.com/script.js"));
        $url->addAsset(new Url("http://example.com/icon.png"));

        $visitedUrls = [$url];

        $siteMap = (new SiteMapRenderer)->render($visitedUrls);

        $this->assertCount(1, $siteMap);

        $this->assertArrayHasKey("url", $siteMap[0]);
        $this->assertEquals("http://example.com", $siteMap[0]["url"]);

        $this->assertArrayHasKey("links", $siteMap[0]);
        $this->assertCount(1, $siteMap[0]["links"]);

        $this->assertArrayHasKey("assets", $siteMap[0]);
        $this->assertCount(2, $siteMap[0]["assets"]);
    }

    /**
     * @test
     */
    public function it_skips_non_urls_when_generating_a_sitemap()
    {
        $url = new Url("http://example.com");
        $url->addLink(new Url("http://iana.org"));
        $url->addAsset(new Url("http://example.com/script.js"));
        $url->addAsset(new Url("http://example.com/icon.png"));

        $visitedUrls = ["non_url_object", $url];

        $siteMap = (new SiteMapRenderer)->render($visitedUrls);

        $this->assertCount(1, $siteMap);
        $this->assertArrayHasKey("url", $siteMap[0]);
        $this->assertArrayHasKey("links", $siteMap[0]);
        $this->assertArrayHasKey("assets", $siteMap[0]);
    }
}
