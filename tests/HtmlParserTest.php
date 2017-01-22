<?php

use SiteCrawler\Factories\DOMFactory;
use SiteCrawler\Factories\UrlFactory;
use SiteCrawler\HtmlParser;
use SiteCrawler\Url;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\DomCrawler\Link;

class HtmlParserTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->domFactory = $this->getMock(DOMFactory::class);
        $this->urlFactory = $this->getMock(UrlFactory::class);
        $this->domCrawler = $this->getMockBuilder(Crawler::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_invokes_the_dom_factory_when_making_a_document_to_represent_a_raw_html_string()
    {
        $htmlParser = new HtmlParser($this->domFactory, $this->urlFactory);

        $url = new Url("http://example.com");
        $html = '\
        <!DOCTYPE html>
        <html>
            <head>
                <title></title>
            </head>
            <body>

            </body>
        </html>';

        $this->domFactory->expects($this->once())
            ->method('makeDOMCrawler')
            ->willReturn($this->domCrawler);

        $htmlParser->makeDocument($url, $html);
    }

    /**
     * @test
     */
    public function it_gets_the_anchor_links_in_the_html_represented_by_a_dom_crawler_object()
    {
        $htmlParser = new HtmlParser($this->domFactory, $this->urlFactory);

        $mockLink = $this->getMockBuilder(Link::class)->disableOriginalConstructor()->getMock();

        $this->domCrawler->expects($this->once())
            ->method('filter')
            ->with('a')
            ->willReturn($this->domCrawler);

        $this->domCrawler->expects($this->once())
            ->method('links')
            ->willReturn([$mockLink]);

        $mockLink->expects($this->once())
            ->method('getUri')
            ->willReturn('http://example.com');

        $this->urlFactory->expects($this->once())
            ->method('makeUrl')
            ->with('http://example.com')
            ->willReturn(new \stdClass);

        $htmlParser->getLinks($this->domCrawler);
    }

    /**
     * @test
     */
    public function it_gets_the_urls_to_the_images_in_the_html_represented_by_a_dom_crawler_object()
    {
        $htmlParser = new HtmlParser($this->domFactory, $this->urlFactory);

        $mockImage = $this->getMockBuilder(Image::class)->disableOriginalConstructor()->getMock();

        $this->domCrawler->expects($this->once())
            ->method('filter')
            ->with('img')
            ->willReturn($this->domCrawler);

        $this->domCrawler->expects($this->once())
            ->method('images')
            ->willReturn([$mockImage]);

        $mockImage->expects($this->once())
            ->method('getUri')
            ->willReturn('http://example.com/image.jpg');

        $this->urlFactory->expects($this->once())
            ->method('makeUrl')
            ->with('http://example.com/image.jpg')
            ->willReturn(new \stdClass);

        $htmlParser->getImages($this->domCrawler);
    }

    /**
     * @test
     */
    public function it_gets_the_urls_to_the_icons_in_the_html_represented_by_a_dom_crawler_object()
    {
        $htmlParser = new HtmlParser($this->domFactory, $this->urlFactory);

        $mockIcon = $this->getMockBuilder(Link::class)->disableOriginalConstructor()->getMock();

        $this->domCrawler->expects($this->exactly(3))
            ->method('filter')
            ->with($this->logicalOr(
                    $this->equalTo('link[rel=icon]'),
                    $this->equalTo('link[rel=apple-touch-icon]'),
                    $this->equalTo('link[rel=apple-touch-startup-image]')
                )
            )
            ->willReturn($this->domCrawler);

        $this->domCrawler->expects($this->exactly(3))
            ->method('links')
            ->willReturn([$mockIcon]);

        $mockIcon->expects($this->exactly(3))
            ->method('getUri')
            ->willReturn('http://example.com/icon.png');

        $this->urlFactory->expects($this->exactly(3))
            ->method('makeUrl')
            ->with('http://example.com/icon.png')
            ->willReturn(new \stdClass);

        $htmlParser->getIcons($this->domCrawler);
    }

    /**
     * @test
     */
    public function it_gets_the_stylesheets_linked_in_the_html_represented_by_a_dom_crawler_object()
    {
        $htmlParser = new HtmlParser($this->domFactory, $this->urlFactory);

        $mockStyleSheet = $this->getMockBuilder(Link::class)->disableOriginalConstructor()->getMock();

        $this->domCrawler->expects($this->once())
            ->method('filter')
            ->with('link[rel=stylesheet]')
            ->willReturn($this->domCrawler);

        $this->domCrawler->expects($this->once())
            ->method('links')
            ->willReturn([$mockStyleSheet]);

        $mockStyleSheet->expects($this->once())
            ->method('getUri')
            ->willReturn('http://example.com/style.css');

        $this->urlFactory->expects($this->once())
            ->method('makeUrl')
            ->with('http://example.com/style.css')
            ->willReturn(new \stdClass);

        $htmlParser->getStyleSheets($this->domCrawler);
    }

    /**
     * @test
     */
    public function it_gets_the_external_scripts_referenced_in_the_html_represented_by_a_dom_crawler_object()
    {
        $htmlParser = new HtmlParser($this->domFactory, $this->urlFactory);

        $mockUrl = $this->getMockBuilder(Url::class)->disableOriginalConstructor()->getMock();

        $mockScriptTag = $this->getMockBuilder(\DOMElement::class)->disableOriginalConstructor()->disableProxyingToOriginalMethods()->getMock();

        $this->domCrawler->expects($this->once())
            ->method('filter')
            ->with('script')
            ->willReturn($this->domCrawler);

        $this->domCrawler->expects($this->once())
            ->method('getUri')
            ->willReturn('http://example.com');

        $this->domCrawler->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$mockScriptTag, $mockScriptTag]));

        $this->urlFactory->expects($this->once())
            ->method('makeUrl')
            ->with('http://example.com')
            ->willReturn($mockUrl);

        $mockScriptTag->expects($this->exactly(2))
            ->method('getAttribute')
            ->with('src')
            ->willReturnOnConsecutiveCalls("", "script.js");

        $this->urlFactory->expects($this->once())
            ->method('makeAssetUrl')
            ->with($mockUrl, "script.js")
            ->willReturn(new \stdClass);

        $htmlParser->getScripts($this->domCrawler);
    }
}
