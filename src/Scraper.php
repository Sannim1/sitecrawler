<?php

namespace SiteCrawler;

use GuzzleHttp\Client;
use SiteCrawler\Crawler;
use SiteCrawler\Url;

class Scraper
{
    /**
     * the client used for making HTTP requests
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient;


    /**
     * object for parsing html
     * @var SiteCrawler\HtmlParser
     */
    protected $htmlParser;

    public function __construct(Client $httpClient, HtmlParser $htmlParser)
    {
        $this->httpClient = $httpClient;
        $this->htmlParser = $htmlParser;
    }

    /**
     * Visits the supplied URL to extract links and assets for the accompanying crawler
     *
     * @param  Crawler $crawler
     * @param  Url     $url
     *
     * @return null
     */
    public function scrape(Crawler $crawler, Url $url)
    {
        $html = $this->visit($url);
        $crawler->markVisited($url);

        $dom = $this->htmlParser->makeDocument($url, $html);

        $links = $this->htmlParser->getLinks($dom);
        foreach ($links as $link) {
            if ($crawler->shouldVisit($link)) {
                $crawler->markForCrawling($link);
            }
            $url->addLink($link);
        }

        $assets = $this->htmlParser->getAssets($dom);
        foreach ($assets as $asset) {
            $url->addAsset($asset);
        }

        return;
    }

    /**
     * Visits a particular URL and returns the response body as a string
     *
     * @param  Url    $url
     *
     * @return string
     */
    public function visit(Url $url)
    {
        $responseHtml = "";
        try {
            $response = $this->httpClient->get($url->getUrl());
            $responseHtml = $response->getBody()->getContents();
        } catch (\Exception $e) {

        }
        return $responseHtml;
    }
}
