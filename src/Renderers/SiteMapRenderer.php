<?php

namespace SiteCrawler\Renderers;

use SiteCrawler\Url;

class SiteMapRenderer extends Renderer implements \SiteCrawler\Contracts\Renderer
{
    public function render($visitedUrls)
    {
        $siteMap = [];

        foreach ($visitedUrls as $visited) {
            if (! $this->isUrl($visited)) {
                continue;
            }

            $siteMap[] = [
                "url" => $visited->getUrl(),
                "links" => $this->renderLinks($visited),
                "assets" => $this->renderAssets($visited),
            ];
        }

        return $siteMap;
    }
}
