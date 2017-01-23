#!/usr/bin/php
<?php

require 'vendor/autoload.php';

if (count($argv) < 2) {
    die("You need to specify a URL as the parameter to this script\n");
}

try {
    $url = new SiteCrawler\Url($argv[1]);
} catch (SiteCrawler\Exceptions\InvalidUrlException $e) {
    die("Invalid URL: {$e->getMessage()}");
} catch (Exception $e) {
    die($e->getMessage());
}

$startTime = time();

$httpClient = new GuzzleHttp\Client;
$htmlParser = new SiteCrawler\HtmlParser(
    new SiteCrawler\Factories\DOMFactory,
    new SiteCrawler\Factories\UrlFactory
);

$scraper = new SiteCrawler\Scraper($httpClient, $htmlParser);

$crawler = new SiteCrawler\Crawler($url, $scraper);

$crawler->start();

$mapMaker = new SiteCrawler\Utils\MapMaker($url);
$mapMaker->createMap($crawler->getVisitedUrls());

$duration = time() - $startTime;
echo "Duration: {$duration} seconds";
