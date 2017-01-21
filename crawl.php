#!/usr/bin/php
<?php

require 'vendor/autoload.php';

if (count($argv) < 2) {
    die("You need to specify a URL as the parameter to this script\n");
}

$url = new SiteCrawler\Url($argv[1]);
$httpClient = new GuzzleHttp\Client;
$htmlParser = new SiteCrawler\HtmlParser(new SiteCrawler\Factories\DOMFactory, new SiteCrawler\Factories\UrlFactory);

$crawler = new SiteCrawler\Crawler($url, $httpClient, $htmlParser);
$crawler->start();
