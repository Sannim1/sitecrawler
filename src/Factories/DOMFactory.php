<?php

namespace SiteCrawler\Factories;

class DOMFactory
{
    public function makeDOM()
    {
        return new \DOMDocument;
    }
}
