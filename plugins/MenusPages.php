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
    
    $sitePath = $this->getSitePath();
    $siteItems = scandir_recursive($sitePath);
    
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
            
            // Load page configs
            $config = $this->getConfig($menuPath.'/config.json');
            
            // Se eh uma pasta significa que temos uma subpagina
            if ($config->visible) {
              $url = $this->url(ltrim(preg_replace("/\/{2,}/", '/', $config->uri), "/"));
              $menus[$menuName][$url] = $config->title;
            }
            
          } else if (file_exists($path.'/config.json') && is_readable($path.'/config.json')) {
            
            // Get page configs
            $params = $this->getConfig($path.'/config.json');
            
            // Check if page is defined as visible
            if ($params->visible) {
              
              // Assoc file to the pages list
              $pages[$this->url($params->uri)] = $params->title;
            }
          }
        }
      }
    }
    
    $this->addViewVar('pages', $pages);
    $this->addViewVar('menus', $menus);
    
    // If theres a sub layout we will render it before
    // the main layout
    $route = $this->getCurrentRoute();
    $subLayout = $route['dirname'].'/sub-layout.phtml';
    $parentSubLayout = dirname($route['dirname']).'/sub-layout.phtml';
    
    if (file_exists($subLayout) || file_exists($parentSubLayout)) {
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
    $result->visible = $result->visible ?? true;
    
    return $result;
  }
  
  private function renderSubLayout(array $route): void {
    
    $template = $this->getTemplate();
      
    // Variables to be appended to the view
    if (isset($route)) {
      foreach ($route as $varName => $varValue) {
        $$varName = $varValue;
      }
    }

    // Caminho base
    $basePath = $this->getBasePath();
    $baseUrl = $this->baseUrl();
    
    $fileTypes = array('content.php', 'content.phtml', 'content.html');
    
    $content = '';
    if (file_exists($route['dirname'].'/content.md')) {
      $parseDown = new ParsedownExtra();
      $content = $parseDown->parse(file_get_contents($route['content']));
    } else {
      foreach (scandir($route['dirname']) as $fileName) {
        if (!is_array($fileName) && in_array($fileName, $fileTypes)) {
          ob_start();
          include $route['dirname'].'/'.$fileName;
          $content = ob_get_clean();
          break;
        }
      }
    }
    
    ob_start();
    if (file_exists($route['dirname'].'/sub-layout.phtml')) {
      
      /** Render sub-layout from the root path **/
      include $route['dirname'].'/sub-layout.phtml';
      
    } else if (file_exists(dirname($route['dirname']).'/sub-layout.phtml')) {
      
      /** Render sub-layout from the parent path **/
      include dirname($route['dirname']).'/sub-layout.phtml';
    }
    $content = ob_get_clean();
    
    $this->addViewVar('content', $content);
  }
}






