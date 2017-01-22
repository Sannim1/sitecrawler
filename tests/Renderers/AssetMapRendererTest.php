<?php

use SiteCrawler\Renderers\AssetMapRenderer;
use SiteCrawler\Url;

class AssetMapRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_generates_an_asset_map_from_an_array_of_visited_urls()
    {
        $url = new Url("http://example.com");
        $url->addLink(new Url("http://iana.org"));
        $url->addAsset(new Url("http://example.com/script.js"));
        $url->addAsset(new Url("http://example.com/icon.png"));

        $visitedUrls = [$url];

        $assetMap = (new AssetMapRenderer)->render($visitedUrls);

        $this->assertCount(1, $assetMap);
        $this->assertArrayHasKey("url", $assetMap[0]);
        $this->assertEquals("http://example.com", $assetMap[0]["url"]);

        $this->assertArrayHasKey("assets", $assetMap[0]);
        $this->assertCount(2, $assetMap[0]["assets"]);
    }

    /**
     * @test
     */
    public function it_skips_non_urls_when_generating_an_asset_map()
    {
        $url = new Url("http://example.com");
        $url->addLink(new Url("http://iana.org"));
        $url->addAsset(new Url("http://example.com/script.js"));
        $url->addAsset(new Url("http://example.com/icon.png"));

        $visitedUrls = ["non_url_object", $url];

        $assetMap = (new AssetMapRenderer)->render($visitedUrls);

        $this->assertCount(1, $assetMap);
        $this->assertArrayHasKey("url", $assetMap[0]);
        $this->assertArrayHasKey("assets", $assetMap[0]);
    }
}
