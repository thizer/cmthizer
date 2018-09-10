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
      $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
      
      foreach (array_keys($this->getRoutes()) as $uri) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>'.$this->url($uri).'</loc>';
        $sitemap .= '<changefreq>monthly</changefreq>';
        $sitemap .= '<priority>'.(($uri == '/') ? '1.00' : '0.80').'</priority>';
        $sitemap .= '</url>';
      }
      $sitemap .= '</urlset>';
      
      header("Content-type: application/xml");
      exit($sitemap);
    }
  }
  
  public function preRun() {}
  public function posRun() {}
}