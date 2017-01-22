<?php

namespace SiteCrawler\Renderers;

use SiteCrawler\Url;

abstract class Renderer
{
    protected function isUrl($url)
    {
        $urlClass = Url::class;
        return $url instanceof $urlClass;
    }

    protected function renderAssets(Url $url)
    {
        $rendered = [];
        $assets = $url->getAssets();
        foreach ($assets as $asset) {
            if ($this->isUrl($asset)) {
                $rendered[] = $asset->getUrl();
            }
        }

        return $rendered;
    }

    protected function renderLinks(Url $url)
    {
        $rendered = [];
        $links = $url->getLinks();
        foreach ($links as $link) {
            if ($this->isUrl($link)) {
                $rendered[] = $link->getUrl();
            }
        }

        return $rendered;
    }
}
