<?php
use CmThizer\Plugins\AbstractPlugin;

class RobotsTxt extends AbstractPlugin {

  public function preUri() {}
  public function posUri() {}

  public function preParams() {}
  public function posParams() {}

  public function prePost() {}
  public function posPost() {}

  public function preRoutes() {}
  public function posRoutes() {

    /**
     * Access to the URI /robots.txt
     */

    if ($this->getUri()->getRouteName() == '/robots.txt') {
      $txt = "User-Agent: *\n\n";

      $txt .= "Disallow: /core\n";
      $txt .= "Disallow: /docs\n";
      $txt .= "Disallow: /plugins\n";
      $txt .= "Disallow: /site\n";
      $txt .= "Disallow: /var\n";
      $txt .= "Disallow: /vendor\n";
      $txt .= "Allow: /\n\n";

      // @todo: This verification is not working here
      $sitemapPlugin = $this->getPlugin('Sitemap');
      if ($sitemapPlugin && $sitemapPlugin->isActive()) {
        $txt .= "sitemap: ".$this->url('/sitemap.xml')."\n\n";
      }

      header("Content-type: text/plain");
      exit($txt);
    }
  }

  public function preRun() {}
  public function posRun() {}
}