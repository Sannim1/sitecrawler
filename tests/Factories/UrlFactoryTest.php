<?php

use SiteCrawler\Factories\UrlFactory;
use SiteCrawler\Url;

class UrlFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->urlFactory = new UrlFactory;
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_returns_an_instance_of_the_url_class()
    {
        $urlString = "http://example.com";
        $url = $this->urlFactory->makeUrl($urlString);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals($urlString, $url->getUrl());
    }

    /**
     * @test
     */
    public function it_makes_a_url_object_from_an_base_url_object_and_a_supplied_path()
    {
        $baseUrl = new Url("http://example.com");
        $path = "/about";

        $url = $this->urlFactory->makeUrlFromPath($baseUrl, $path);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals("http://example.com/about", $url->getUrl());
    }

    /**
     * @test
     */
    public function it_makes_a_url_object_for_an_asset_from_a_base_url_and_the_assets_source()
    {
        $baseUrl = new Url("http://example.com");
        $relativeSource = "/asset.js";

        $fullUrl = $this->urlFactory->makeAssetUrl($baseUrl, $relativeSource);
        $this->assertEquals("http://example.com/asset.js", $fullUrl->getUrl());

        $absoluteSource = "http://example.com/asset.js";
        $fullUrl = $this->urlFactory->makeAssetUrl($baseUrl, $absoluteSource);
        $this->assertEquals("http://example.com/asset.js", $fullUrl->getUrl());

        $externalSource = "//external-domain.com/script.js";
        $fullUrl = $this->urlFactory->makeAssetUrl($baseUrl, $externalSource);
        $this->assertEquals("http://external-domain.com/script.js", $fullUrl->getUrl());
    }
}
