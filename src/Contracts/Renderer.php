<?php

namespace SiteCrawler\Contracts;

interface Renderer
{
    /**
     * Renders the supplied array of visited urls
     *
     * @param  array $visitedUrls
     *
     * @return array
     */
    public function render($visitedUrls);
}
