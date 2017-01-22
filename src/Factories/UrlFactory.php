<?php

namespace SiteCrawler\Factories;

use SiteCrawler\Url;

class UrlFactory
{
    /**
     * makes a Url object representing the supplied URL string
     *
     * @param  string $url
     *
     * @return Url
     */
    public function makeUrl($url)
    {
        return new Url($url);
    }

    /**
     * Makes a Url object from the supplied base url and path
     *
     * @param  Url    $parentUrl
     * @param  string $path
     *
     * @return Url
     */
    public function makeUrlFromPath(Url $parentUrl, $path)
    {
        $scheme = $parentUrl->getScheme();
        $domain = $parentUrl->getDomain();
        $path = ltrim($path, "/");

        return new Url("{$scheme}://{$domain}/$path");
    }

    /**
     * Makes a url object from a base url and source attribute
     *
     * @param  Url    $baseUrl
     * @param  string $sourceString
     *
     * @return Url
     */
    public function makeAssetUrl(Url $baseUrl, $sourceString)
    {
        $scheme = parse_url($sourceString, PHP_URL_SCHEME);
        if (isset($scheme)) {
            return new Url($sourceString);
        }

        if (strpos($sourceString, "//") === 0) {
            $scheme = $baseUrl->getScheme();
            return new Url("{$scheme}:{$sourceString}");
        }

        return $this->makeUrlFromPath($baseUrl, $sourceString);
    }
}
