<?php
use CmThizer\Plugins\AbstractPlugin;

class Sitemap extends AbstractPlugin {
  
  public function preUri(): void {}
  public function posUri(): void {}
  
  public function preParams(): void {}
  public function posParams(): void {}
  
  public function prePost(): void {}
  public function posPost(): void {}
  
  public function preRoutes(): void {}
  public function posRoutes(): void {
    
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
  
  public function preRun(): void {}
  public function posRun(): void {}
}