<?php
namespace SiteCrawler;

use SiteCrawler\Scraper;
use SiteCrawler\Url;

class Crawler
{
    /**
     * an array of URLs that are yet to be visited
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
     * object for visiting URLs in order to collect link and asset information
     *
     * @var SiteCrawler\Scraper
     */
    protected $scraper;

    public function __construct(Url $rootUrl, Scraper $scraper)
    {
        $this->rootUrl = $rootUrl;
        $this->scraper = $scraper;
    }

    /**
     * start crawling from the crawler's root URL and return all reachable URLs within the same domain
     *
     * @return array
     */
    public function start()
    {
        $this->visitedUrls = [];
        $this->urlsToVisit = [];

        $this->markForCrawling($this->rootUrl);

        $i = 1;
        while ($nextUrl = array_pop($this->urlsToVisit)) {
            echo $i;
            $this->scraper->scrape($this, $nextUrl);
            $i++;
        }

        return;
    }

    /**
     * returns an array of all the URLs already visited by the crawler
     *
     * @return array
     */
    public  function getVisitedUrls()
    {
        return array_values($this->visitedUrls);
    }

    /**
     * adds the supplied URL to the queue of URLs which the crawler still has to visit
     *
     * @param  Url    $url
     *
     * @return null
     */
    public function markForCrawling(Url $url)
    {
        $this->urlsToVisit[$url->getUrl()] = $url;
    }

    /**
     * marks a particular URL as being visited by the crawler
     *
     * @param  Url    $url
     *
     * @return null
     */
    public function markVisited(Url $url)
    {
        $urlString = $url->getUrl();
        $this->visitedUrls[$urlString] = $url;
        echo "\t\t Visited {$urlString} \n";
    }

    /**
     * checks if the crawler should visit the specified URL
     *
     * @param  Url    $url
     *
     * @return boolean
     */
    public function shouldVisit(Url $url)
    {
        if (! $url->hasSameDomain($this->rootUrl)) {
            return false;
        }
        if (isset($this->visitedUrls[$url->getUrl()])) {
            return false;
        }
        if (! in_array($url->getScheme(), ["http", "https"])) {
            return false;
        }
        return true;
    }
}
