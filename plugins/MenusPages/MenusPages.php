<?php
use CmThizer\Plugins\AbstractPlugin;

class MenusPages extends AbstractPlugin {
  
  /**
   * That is exactaly the right order which
   * this methods will be executed.
   */
  
  public function preUri(): void {} // 1
  public function posUri(): void {} // 2
  
  public function preParams(): void {} // 3
  public function posParams(): void {} // 4
  
  public function prePost(): void {} // 5
  public function posPost(): void {} // 6

  public function preRoutes(): void {} // 7
  public function posRoutes(): void {} // 8

  public function preRun(): void {
    
    $routeName = $this->getCmThizer()->getUri()->getRouteName();
    
    $sitePath = $this->getCmThizer()->getSitePath();
    $siteItems = scandir_recursive($sitePath);
    
    $pages = array();
    $posts = array();
    
    foreach ($siteItems as $path => $content) {
      if (is_array($content)) {
        foreach (array_keys($content) as $subPath) {
          
          if (is_dir($subPath)) {
            $posts[$path] = $content;
          } else {
            $pages[$path] = $content;
          }
          
        }
      }
    }
    echo dump($pages, false);
    echo dump($posts, false);
    exit;
  }
  
  public function posRun(): void {} // 10
  
  private function 
}






