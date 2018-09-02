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
    
    $sitePath = $this->getCmThizer()->getSitePath();
    $siteItems = scandir_recursive($sitePath);
    $baseUrl = $this->getCmThizer()->getBaseUrl();
    
    // Organize site pages (and menus)
    $pages = array();
    $menus = array();
    foreach ($siteItems as $path => $content) {
      
      // Ignora arquivos soltos na pasta
      if (is_array($content)) {
        foreach (array_keys($content) as $menuPath) {
          if (is_dir($menuPath)) {
            
            // The menu name is the name of the parent folder
            $menuName = preg_replace("/^.+\//", '', $path);
            
            $config = $this->getConfig($menuPath.'/config.json');
            
            // Se eh uma pasta significa que temos uma subpagina
            $url = $baseUrl.ltrim(preg_replace("/\/{2,}/", '/', $config->uri), "/");
            $menus[$menuName][$url] = $config->title;
            
          } else if (file_exists($path.'/config.json') && is_readable($path.'/config.json')) {
            
            // Assoc file to the pages list
            $params = json_decode(file_get_contents($path.'/config.json'));
            $pages[$baseUrl.$params->uri] = $params->title;
          }
        }
      }
    }
    
    $this->getCmThizer()->addViewVar('pages', $pages);
    $this->getCmThizer()->addViewVar('menus', $menus);
    
    // If theres a sub layout we will render it before
    // the main layout
    $route = $this->getCmThizer()->getCurrentRoute();
    if (file_exists($route['dirname'].'/sub-layout.phtml')) {
      $this->renderSubLayout($route);
    }
  }
  
  public function posRun(): void {} // 10
  
  private function getConfig(string $file): stdClass {
    $result = new stdClass();
    if (file_exists($file) && is_readable($file)) {
      $result = json_decode(file_get_contents($file));
    } else {
      throw new Exception("The file '$file' was not found or is not readable");
    }
    
    $result->uri = $result->uri ?? '/not-found';
    $result->title = $result->title ?? 'My Website';
    
    return $result;
  }
  
  private function renderSubLayout(array $route): void {
    
    $template = $this->getCmThizer()->getTemplate();
      
    // Variables to be appended to the view
    if (isset($route)) {
      foreach ($route as $varName => $varValue) {
        $$varName = $varValue;
      }
    }

    // Caminho base
    $basePath = $this->getCmThizer()->getBasePath();
    $baseUrl = $this->getCmThizer()->getBaseUrl();
    
    $content = '';
    if (file_exists($route['dirname'].'/content.md')) {
      $parseDown = new ParsedownExtra();
      $content = $parseDown->parse(file_get_contents($route['content']));
    }
    
    ob_start();
    include $route['dirname'].'/sub-layout.phtml';
    $content = ob_get_clean();
    
    $this->getCmThizer()->addViewVar('content', $content);
  }
}






