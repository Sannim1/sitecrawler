<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use SiteCrawler\Crawler;
use SiteCrawler\HtmlParser;
use SiteCrawler\Scraper;
use SiteCrawler\Url;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class ScraperTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->url = new Url("http://example.com");
        $this->mockDom = $this->getMockBuilder(DomCrawler::class)->disableOriginalConstructor()->getMock();
        $this->htmlParser = $this->getMockBuilder(HtmlParser::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_visits_a_particular_url_and_returns_the_response_body_as_a_string()
    {
        $mockHttpHandler = new MockHandler([
            new Response(200, [], "<html></html>")
        ]);
        $httpClient = new Client(['handler' => $mockHttpHandler]);
        $scraper = new Scraper($httpClient, $this->htmlParser);

        $responseHtml = $scraper->visit($this->url);
        $this->assertEquals("<html></html>", $responseHtml);
    }

    /**
     * @test
     */
    public function it_visits_a_particular_url_to_extract_links_and_assets_for_a_specified_crawler()
    {
        $mockCrawler = $this->getMockBuilder(Crawler::class)->disableOriginalConstructor()->getMock();
        $mockHttpHandler = new MockHandler([
            new Response(200, [], "<html></html>")
        ]);
        $httpClient = new Client(['handler' => $mockHttpHandler]);
        $scraper = new Scraper($httpClient, $this->htmlParser);

        $mockCrawler->expects($this->once())
            ->method('markVisited')
            ->with($this->url)
            ->willReturn(null);

        $this->htmlParser->expects($this->once())
            ->method('makeDocument')
            ->with($this->url, "<html></html>")
            ->willReturn($this->mockDom);

        $this->htmlParser->expects($this->once())
            ->method('getLinks')
            ->with($this->mockDom)
            ->willReturn([$this->url]);

        $mockCrawler->expects($this->once())
            ->method('shouldVisit')
            ->with($this->url)
            ->willReturn(true);

        $mockCrawler->expects($this->once())
            ->method('markForCrawling')
            ->with($this->url)
            ->willReturn(true);

        $this->htmlParser->expects($this->once())
            ->method('getAssets')
            ->with($this->mockDom)
            ->willReturn([$this->url]);

        $scraper->scrape($mockCrawler, $this->url);
    }

    /**
     * @test
     */
    public function it_checks_if_a_particular_url_links_to_an_html_page()
    {
        $mockHttpHandler = new MockHandler([
            new Response(200, ["Content-Type" => "text/html"]),
            new Response(200, []),
            new ClientException('Not Found', new Request('GET', '/test')),
        ]);
        $httpClient = new Client(['handler' => $mockHttpHandler]);
        $scraper = new Scraper($httpClient, $this->htmlParser);

        $this->assertTrue($scraper->linksToHtmlPage($this->url));
        $this->assertFalse($scraper->linksToHtmlPage($this->url));
        $this->assertFalse($scraper->linksToHtmlPage($this->url));
    }
}
