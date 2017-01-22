<?php

use SiteCrawler\Factories\DOMFactory;
use SiteCrawler\Url;
use Symfony\Component\DomCrawler\Crawler;

class DOMFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->domFactory = new DOMFactory;
        $this->exampleHtml = '\
        <!doctype html>
        <html>
            <head>
                <title>Example Domain</title>
                <meta charset="utf-8" />
                <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
            </head>

            <body>
            <div>
                <h1>Example Domain</h1>
                <p>This domain is established to be used for illustrative examples in documents. You may use this
                domain in examples without prior coordination or asking for permission.</p>
                <p><a href="http://www.iana.org/domains/example">More information...</a></p>
            </div>
            </body>
        </html>';
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_makes_a_new_instance_of_a_dom_crawler_object_from_a_url_and_its_corresponding_html_string()
    {
        $urlString = "http://example.com";
        $url = new Url($urlString);

        $domCrawler = $this->domFactory->makeDOMCrawler($url, $this->exampleHtml);

        $this->assertInstanceOf(Crawler::class, $domCrawler);
        $this->assertEquals($domCrawler->getUri(), $urlString);
    }
}
