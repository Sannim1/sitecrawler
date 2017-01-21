<?php
namespace SiteCrawler;

use GuzzleHttp\Client;
use SiteCrawler\HtmlParser;
use SiteCrawler\Url;

class Crawler
{
    /**
     * an array of URLs that are yet to be crawled
     * @var array
     */
    protected $urlsToVisit;

    /**
     * an array of URLs that have been visited
     * @var array
     */
    protected $visitedUrls;

    /**
     * URL from which the crawler starts
     * @var SiteCrawler\Url
     */
    protected $rootUrl;

    /**
     * the client used by this crawler object for making HTTP requests
     * @var GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * object for parsing html
     * @var SiteCrawler\HtmlParser
     */
    protected $htmlParser;

    public function __construct(Url $rootUrl, Client $httpClient, HtmlParser $htmlParser)
    {
        $this->rootUrl = $rootUrl;
        $this->httpClient = $httpClient;
        $this->htmlParser = $htmlParser;
    }

    public function start()
    {
        $this->visitedUrls = [];
        $this->urlsToVisit = [$this->rootUrl];

        while (count($this->urlsToVisit) > 0) {
            $this->crawl(array_shift($this->urlsToVisit));
        }

        foreach ($this->visitedUrls as $visited) {
            print_r($visited);
        }
    }

    private function crawl(Url $url)
    {
        $html = $this->visit($url);
        $this->visitedUrls[] = $url;

        $domDocument = $this->htmlParser->makeDocument($html);

        $links = $this->htmlParser->getLinks($domDocument);
        foreach ($links as $link) {
            if ($this->shouldBeVisited($link)) {
                $this->urlsToVisit[] = $link;
            }
            $url->addLink($link);
        }

        $assets = $this->htmlParser->getAssets($domDocument);
        foreach ($assets as $asset) {
            $url->addAsset($asset);
        }

        return;
    }

    private function visit(Url $url)
    {
        $responseHtml = "";
        try {
            $response = $this->httpClient->get($url->getUrl());
            $responseHtml = $response->getBody()->getContents();
        } catch (Exception $e) {
            dd($e);
        }
        return $responseHtml;
    }

    private function shouldBeVisited(Url $url)
    {
        if (! $url->hasSameDomain($this->rootUrl)) {
            return false;
        }
        foreach ($this->visitedUrls as $visitedUrl) {
            if ($visitedUrl->getUrl() == $url->getUrl()) {
                return false;
            }
        }
        return true;
    }
}
