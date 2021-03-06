<?php
namespace PiggyBox\SitemapBundle\Twig\Extension;

class SitemapExtension extends \Twig_Extension
{
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getFilters()
    {
        return array(
            'piggybox_sitemap_url_absolute' => new \Twig_Filter_Method($this, 'absoluteUrl'),
            'piggybox_sitemap_date' => new \Twig_Filter_Method($this, 'formatDate'),
        );
    }

    public function absoluteUrl($path)
    {
        return $this->baseUrl.'/'.ltrim($path, '/');
    }

    public function formatDate(\DateTime $date)
    {
        // YYYY-MM-DDThh:mmTZD
        return $date->format('Y-m-d');
    }

    public function getName()
    {
        return 'sitemap';
    }
}
