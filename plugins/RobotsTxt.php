<?php
use CmThizer\Plugins\AbstractPlugin;

class RobotsTxt extends AbstractPlugin {
  
  public function preUri(): void {}
  public function posUri(): void {}
  
  public function preParams(): void {}
  public function posParams(): void {}
  
  public function prePost(): void {}
  public function posPost(): void {}
  
  public function preRoutes(): void {}
  public function posRoutes(): void {
    
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
      $txt .= "Disallow: /vendor\n\n";
      
      // @todo: This verification is not working here
      $sitemapPlugin = $this->getPlugin('Sitemap');
      if ($sitemapPlugin && $sitemapPlugin->isActive()) {
        $txt .= "sitemap: ".$this->url('/sitemap.xml')."\n";
      }

      header("Content-type: text/plain");
      exit($txt);
    }
  }
  
  public function preRun(): void {}
  public function posRun(): void {}
}