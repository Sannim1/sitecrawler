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

$visitedUrls = $crawler->start();

$siteMap = (new SiteCrawler\Renderers\SiteMapRenderer)->render($visitedUrls);
$assetMap = (new SiteCrawler\Renderers\AssetMapRenderer)->render($visitedUrls);

$outputDirectory = getcwd() . "/sitemaps/" . str_replace("/", "", $url->getUrl());
if (! is_dir($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
}
$siteMapFileName = "{$outputDirectory}/sitemap.json";
$assetMapFileName = "{$outputDirectory}/assetmap.json";

file_put_contents($siteMapFileName, json_encode($siteMap));
file_put_contents($assetMapFileName, json_encode($assetMap));

echo "DONE!!!" . PHP_EOL;
echo "Sitemap can be found at: {$siteMapFileName}" . PHP_EOL;
echo "Asset map can be found at: {$assetMapFileName}" . PHP_EOL;

$duration = time() - $startTime;
echo "Duration: {$duration} seconds";
