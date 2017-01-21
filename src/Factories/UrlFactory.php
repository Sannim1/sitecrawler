<?php

namespace SiteCrawler\Factories;

use SiteCrawler\Url;

class UrlFactory
{
    public function makeUrl($url)
    {
        return new Url($url);
    }
}
