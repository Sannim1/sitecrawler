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
        $this->urlsToVisit = [];

        $this->markForCrawling($this->rootUrl);

        // $this->crawl(array_pop($this->urlsToVisit));
        $i = 1;
        while (count($this->urlsToVisit) > 0) {
            echo $i;
            $i++;
            $this->crawl(array_pop($this->urlsToVisit));
        }

        foreach ($this->visitedUrls as $visitedUrl) {
            var_dump($visitedUrl->getUrl());
        }
        var_dump(count($this->visitedUrls));
        // var_dump(count($this->urlsToVisit));
    }

    private function markForCrawling(Url $url)
    {
        $this->urlsToVisit[$url->getUrl()] = $url;
    }

    private function markVisited(Url $url)
    {
        $this->visitedUrls[$url->getUrl()] = $url;
    }

    private function crawl(Url $url)
    {
        $html = $this->visit($url);
        $this->markVisited($url);
        echo "\t\t Visited {$url->getUrl()} \n";

        $domCrawler = $this->htmlParser->makeDocument($url, $html);

        $links = $this->htmlParser->getLinks($domCrawler);
        foreach ($links as $link) {
            if ($this->shouldBeVisited($link)) {
                $this->markForCrawling($link);
            }
            $url->addLink($link);
        }

        $assets = $this->htmlParser->getAssets($domCrawler, $url);
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
        } catch (\Exception $e) {

        }
        return $responseHtml;
    }

    private function shouldBeVisited(Url $url)
    {
        if (! $url->hasSameDomain($this->rootUrl)) {
            return false;
        }
        if (isset($this->visitedUrls[$url->getUrl()])) {
            echo "{$url->getUrl()} has already been visited. \n";
            return false;
        }
        if (! in_array($url->getScheme(), ["http", "https"])) {
            return false;
        }
        if (! $this->linksToHtmlPage($url)) {
            echo "{$url->getUrl()} does not link to an HTML page. \n";
            return false;
        }
        return true;
    }

    private function linksToHtmlPage(Url $url)
    {
        try {
            $headResponse = $this->httpClient->head($url->getUrl());
        } catch (\Exception $e) {
            return false;
        }
        $contentTypeHeaders = $headResponse->getHeader("Content-Type");
        foreach ($contentTypeHeaders as $headerValue) {
            $isHtmlHeader = (strpos($headerValue, "text/html") === 0);
            if ($isHtmlHeader) {
                return true;
            }
        }
        return false;
    }
}
