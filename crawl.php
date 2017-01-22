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

$httpClient = new GuzzleHttp\Client;
$htmlParser = new SiteCrawler\HtmlParser(new SiteCrawler\Factories\DOMFactory, new SiteCrawler\Factories\UrlFactory);

$crawler = new SiteCrawler\Crawler($url, $httpClient, $htmlParser);
// $r = $httpClient->head($url->getUrl());
// var_dump($r->getHeader("Content-Type"));
// $headResponse = $httpClient->head($url->getUrl());
// $contentTypeHeaders = $headResponse->getHeader("Content-Type");
// foreach ($contentTypeHeaders as $headerValue) {
//     $isHtmlHeader = (strpos($headerValue, "text/html") === 0);
//     if ($isHtmlHeader) {
//         echo $headerValue;
//     }
// }
// var_dump($r->getBody()->getContents());
// $html = $httpClient->get($url->getUrl())->getBody()->getContents();
// $domCrawler = $htmlParser->makeDocument($url, $html);
// $a = $domCrawler->filter("a")->links();
// $a = $domCrawler->filter("link")->links();
// $a = $domCrawler->filter("img")->images();
// $a = $domCrawler->filter("img");
// foreach ($a as $s) {
//     var_dump($s->getAttribute("src"));
// }
// foreach ($a as $i) {
//     var_dump($i->getUri());
// }
// var_dump(count($a));
// $htmlParser->getLinks($domCrawler);
// var_dump($htmlParser->getImages($domCrawler));
// $images = $htmlParser->getAssets($domCrawler);
// foreach ($images as $i) {
//     var_dump($i->getUrl());
// }
// var_dump(count($images));
// $scripts = $domCrawler->filter("script");
// foreach ($scripts as $s) {
//     var_dump($s->getAttribute("src"));
// }
// var_dump($scripts->getUri());
// var_dump(count($scripts));
$startTime = time();
$crawler->start();
$duration = time() - $startTime;

echo "Duration: {$duration}";
