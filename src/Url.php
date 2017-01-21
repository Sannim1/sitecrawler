<?php

namespace SiteCrawler;

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
     * an array of URLs linked to this URL
     * @var array
     */
    protected $links;

    /**
     * an array of assets(js, css) linked to this URL
     * @var array
     */
    protected $assets;

    public function __construct($url)
    {
        $url = rtrim($url, "/");

        if (strpos($url, "http") !== 0) {
            $url = "http://{$url}";
        }

        $this->url = $url;
        $this->domain = parse_url($url, PHP_URL_HOST);
        $this->links = [];
        $this->assets = [];
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getAssets()
    {
        return $this->assets;
    }

    public function addLink(Url $url)
    {
        foreach ($this->links as $link) {
            if ($link->getUrl() === $url->getUrl()) {
                return;
            }
        }
        $this->link[] = $url;
        return;
    }

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

    public function hasSameDomain(Url $url)
    {
        return $url->getDomain() === $this->getDomain();
    }
}
