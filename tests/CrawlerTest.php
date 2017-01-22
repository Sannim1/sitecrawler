<?php

use SiteCrawler\Crawler;
use SiteCrawler\MapMaker;
use SiteCrawler\Scraper;
use SiteCrawler\Url;

class CrawlerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mockUrl = $this->getMockBuilder(Url::class)->disableOriginalConstructor()->getMock();
        $this->mockScraper = $this->getMockBuilder(Scraper::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_gets_all_the_urls_that_are_reachable_from_the_supplied_root_url()
    {
        $rootUrl = new Url("http://example.com");

        $crawler = new Crawler($rootUrl, $this->mockScraper, new MapMaker($this->mockUrl));
        $this->mockScraper->expects($this->once())
            ->method('scrape')
            ->with($crawler, $rootUrl)
            ->willReturn(null);

        $crawler->start();
    }

    /**
     * @test
     */
    public function it_adds_a_supplied_url_to_the_queue_of_links_to_be_crawled()
    {
        $crawler = new Crawler($this->mockUrl, $this->mockScraper, new MapMaker($this->mockUrl));

        $this->mockUrl->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com');

        $crawler->markForCrawling($this->mockUrl);
    }

    /**
     * @test
     */
    public function it_adds_a_supplied_url_to_the_list_of_visited_urls()
    {
        $crawler = new Crawler($this->mockUrl, $this->mockScraper, new MapMaker($this->mockUrl));

        $this->mockUrl->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com');

        $crawler->markVisited($this->mockUrl);
    }

    /**
     * @test
     */
    public function it_checks_if_the_crawler_should_visit_a_specified_url()
    {
        $rootUrl = new Url("http://example.com");
        $urlToVisit = new Url("http://example.com/about");

        $crawler = new Crawler($rootUrl, $this->mockScraper, new MapMaker($this->mockUrl));
        $this->mockScraper->expects($this->once())
            ->method('linksToHtmlPage')
            ->willReturn(true);

        $this->assertTrue($crawler->shouldVisit($urlToVisit));
    }

    /**
     * @test
     */
    public function it_only_visits_urls_within_the_same_domain_as_the_root_url()
    {
        $rootUrl = new Url("http://example.com");
        $urlToVisit = new Url("http://not.example.com");

        $crawler = new Crawler($rootUrl, $this->mockScraper, new MapMaker($this->mockUrl));

        $this->assertFalse($crawler->shouldVisit($urlToVisit));
    }

    /**
     * @test
     */
    public function it_does_not_visit_a_particular_url_more_than_once()
    {
        $rootUrl = new Url("http://example.com");
        $urlToVisit = new Url("http://example.com/about");

        $crawler = new Crawler($rootUrl, $this->mockScraper, new MapMaker($this->mockUrl));
        $crawler->markVisited($urlToVisit);

        $this->assertFalse($crawler->shouldVisit($urlToVisit));
    }

    /**
     * @test
     */
    public function it_only_visits_urls_with_http_or_https_protocols()
    {
        $rootUrl = new Url("http://example.com");
        $urlToVisit = new Url("ftp://example.com");

        $crawler = new Crawler($rootUrl, $this->mockScraper, new MapMaker($this->mockUrl));

        $this->assertFalse($crawler->shouldVisit($urlToVisit));
    }

    /**
     * @test
     */
    public function it_only_visits_urls_that_link_to_an_html_page()
    {
        $rootUrl = new Url("http://example.com");
        $urlToVisit = new Url("http://example.com/document.pdf");

        $crawler = new Crawler($rootUrl, $this->mockScraper, new MapMaker($this->mockUrl));

        $this->mockScraper->expects($this->once())
            ->method('linksToHtmlPage')
            ->with($urlToVisit)
            ->willReturn(false);

        $this->assertFalse($crawler->shouldVisit($urlToVisit));
    }
}
