<?php

use SiteCrawler\Url;

class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_an_object_representing_the_supplied_url_string()
    {
        $urlString = "http://example.com";
        $url = new Url($urlString);

        $this->assertEquals($urlString, $url->getUrl());
        $this->assertEquals("example.com", $url->getDomain());
        $this->assertEquals("http", $url->getScheme());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_the_supplied_url_string_does_not_contain_a_url_scheme()
    {
        $invalidUrlString = "example.com";
        $this->setExpectedException(SiteCrawler\Exceptions\InvalidUrlException::class);
        $url = new Url($invalidUrlString);
    }

    /**
     * @test
     */
    public function it_strips_the_anchor_component_from_the_supplied_url_string()
    {
        $urlWithAnchor = "http://example.com#anchor";
        $url = new Url($urlWithAnchor);

        $this->assertEquals("http://example.com", $url->getUrl());
    }

    /**
     * @test
     */
    public function it_strips_the_query_component_from_the_supplied_url_string()
    {
        $urlWithQuery = "http://example.com?field=value";
        $url = new Url($urlWithQuery);

        $this->assertEquals("http://example.com", $url->getUrl());
    }

    /**
     * @test
     */
    public function it_keeps_track_of_links_between_urls()
    {
        $linkedUrlString = "http://www.iana.org/domains/example";

        $url = new Url("http://example.com");
        $linkedUrl = new Url($linkedUrlString);

        $this->assertCount(0, $url->getLinks());

        $url->addLink($linkedUrl);

        $links = $url->getLinks();
        $this->assertCount(1, $links);

        $this->assertInstanceOf(Url::class, $links[0]);
        $this->assertEquals($links[0]->getUrl(), $linkedUrlString);
    }

    /**
     * @test
     */
    public function it_only_keeps_track_of_unique_links()
    {
        $linkedUrlString = "http://www.iana.org/domains/example";

        $url = new Url("http://example.com");
        $linkedUrl = new Url($linkedUrlString);

        $url->addLink($linkedUrl);

        $links = $url->getLinks();
        $this->assertCount(1, $links);

        $this->assertInstanceOf(Url::class, $links[0]);
        $this->assertEquals($links[0]->getUrl(), $linkedUrlString);

        $url->addLink(new Url($linkedUrlString));
        $this->assertCount(1, $url->getLinks());
    }

    /**
     * @test
     */
    public function it_keeps_track_of_the_url_of_dependent_assets()
    {
        $urlString = "http://example.com";
        $assetUrlString = "http://example.com/styles.css";

        $url = new Url($urlString);
        $assetUrl = new Url($assetUrlString);

        $this->assertCount(0, $url->getAssets());

        $url->addAsset($assetUrl);

        $assets = $url->getAssets();
        $this->assertCount(1, $assets);

        $this->assertInstanceOf(Url::class, $assets[0]);
        $this->assertEquals($assets[0]->getUrl(), $assetUrlString);
    }

    /**
     * @test
     */
    public function it_only_keeps_track_of_unique_assets()
    {
        $assetUrlString = "http://example.com/styles.css";

        $url = new Url("http://example.com");
        $assetUrl = new Url($assetUrlString);

        $url->addAsset($assetUrl);

        $assets = $url->getAssets();
        $this->assertCount(1, $assets);

        $this->assertInstanceOf(Url::class, $assets[0]);
        $this->assertEquals($assets[0]->getUrl(), $assetUrlString);

        $url->addAsset(new Url($assetUrlString));
        $this->assertCount(1, $url->getAssets());
    }

    /**
     * @test
     */
    public function it_checks_if_two_urls_belong_to_the_same_domain()
    {
        $exampleUrl = new Url("http://example.com");
        $internalUrl = new Url("http://example.com/about");
        $externalUrl = new Url("http://www.iana.org/domains/example");

        $this->assertTrue($exampleUrl->hasSameDomain($internalUrl));
        $this->assertFalse($exampleUrl->hasSameDomain($externalUrl));
    }
}
