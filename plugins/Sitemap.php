<?php
use CmThizer\Plugins\AbstractPlugin;

class Sitemap extends AbstractPlugin {

  public function preUri() {}
  public function posUri() {}

  public function preParams() {}
  public function posParams() {}

  public function prePost() {}
  public function posPost() {}

  public function preRoutes() {}
  public function posRoutes() {

    /**
     * Access to the URI /sitemap.xml
     */

    if ($this->getUri()->getRouteName() == '/sitemap.xml') {

      $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
      $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

      foreach ($this->getRoutes() as $uri => $route) {
        if (!isset($route['visible']) || $route['visible']) {
          $sitemap .= '<url>';
          $sitemap .= '<loc>'.$this->url($uri).'</loc>';
          if (isset($route['imgbody'])) {
            $sitemap .= '<image:image><image:loc>'.$this->url($route['imgbody']).'</image:loc></image:image>';
          }
          $sitemap .= '<changefreq>'.(($uri == '/') ? 'monthly' : 'daily').'</changefreq>';
          $sitemap .= '<priority>'.(($uri == '/') ? '1.00' : '0.80').'</priority>';
          if (isset($route['created'])) {
            $sitemap .= '<lastmod>'.$route['created'].'</lastmod>';
          }
          $sitemap .= '</url>';
        }
      }
      $sitemap .= '</urlset>';

      header("Content-type: application/xml");
      exit($sitemap);
    }
  }

  public function preRun() {}
  public function posRun() {}
}