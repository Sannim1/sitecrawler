<?php

namespace SiteCrawler\Renderers;

use SiteCrawler\Url;

class AssetMapRenderer extends Renderer implements \SiteCrawler\Contracts\Renderer
{
    public function render($visitedUrls)
    {
        $assetMap = [];

        foreach ($visitedUrls as $visited) {
            if (! $this->isUrl($visited)) {
                continue;
            }

            $assetMap[] = [
                "url" => $visited->getUrl(),
                "assets" => $this->renderAssets($visited),
            ];
        }

        return $assetMap;
    }
}
