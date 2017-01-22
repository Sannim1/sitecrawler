<?php

namespace SiteCrawler;

use SiteCrawler\Exceptions\InvalidUrlException;

class Url
{
    /**
     * the URL string being represented by this class
     * @var string
     */
    protected $url;

    /**
     * the domain name for this URL
     * @var string
     */
    protected $domain;

    /**
     * the protocol for this URL(e.g http, https)
     * @var string
     */
    protected $scheme;

    /**
     * an array of URLs linked to this URL
     * @var array
     */
    protected $links;

    /**
     * an array of assets(js, css, images) linked to this URL
     * @var array
     */
    protected $assets;

    public function __construct($url)
    {
        $parsedUrl = parse_url($url);
        if (! isset($parsedUrl["scheme"])) {
            throw new InvalidUrlException("You need to specify a URL scheme(e.g http, https)");
        }

        $url = rtrim($url, "/");
        $url = $this->removeAnchor($url);
        $url = $this->removeQueryString($url);

        $this->url = $url;
        $this->scheme = $parsedUrl["scheme"];
        $this->domain = "";
        if (isset($parsedUrl["host"])) {
            $this->domain = $parsedUrl["host"];
        }
        $this->links = [];
        $this->assets = [];
    }

    /**
     * gets the URL string represented by this object
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * gets the domain of the URL represented by this object
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * gets the scheme of the URL represented by this object
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * gets all of the unique URLs to which this URL is linked to
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * gets all of the unique assets on which the resource at this URL depends on
     *
     * @return array
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * keeps track of a link to the specified url object
     *
     * @param Url $url
     */
    public function addLink(Url $url)
    {
        foreach ($this->links as $link) {
            if ($link->getUrl() === $url->getUrl()) {
                return;
            }
        }
        $this->links[] = $url;
        return;
    }

    /**
     * keeps track of an asset dependence on the resource at the specified URL.
     *
     * @param Url $url
     */
    public function addAsset(Url $url)
    {
        foreach ($this->assets as $asset) {
            if ($asset->getUrl() === $url->getUrl()) {
                return;
            }
        }
        $this->assets[] = $url;
        return;
    }

    /**
     * checks if the supplied url exists in the same domain as the represented URL
     *
     * @param  Url     $url
     *
     * @return boolean
     */
    public function hasSameDomain(Url $url)
    {
        return $url->getDomain() === $this->getDomain();
    }

    /**
     * strips the query portion from the supplied url string
     *
     * @param  string $url
     *
     * @return string
     */
    private function removeQueryString($url)
    {
        $queryStartsAt = strpos($url, "?");
        if ($queryStartsAt === false) {
            return $url;
        }
        return substr($url, 0, $queryStartsAt);
    }

    /**
     * strips the anchor portion from the supplied url string
     *
     * @param  string $url
     *
     * @return string
     */
    private function removeAnchor($url)
    {
        $anchorStartsAt = strpos($url, "#");
        if ($anchorStartsAt === false) {
            return $url;
        }
        return substr($url, 0, $anchorStartsAt);
    }
}
