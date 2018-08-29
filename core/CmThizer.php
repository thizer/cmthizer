<?php
class CmThizer {
  
  private $template = 'template.phtml';
  
  private $landingPage = 'landing-page.phtml';
  
  private $uri;
  
  private $params = array();
  
  private $post = array();
  
  private $routes = array();
  
  public function __construct() {
    try {
      
      $this->uri = new \CmThizer\Uri();
      
      $this->resolveParams();
      $this->resolvePost();
      $this->resolveRoutes();
      
    } catch (Exception $ex) {
      dump($ex);
    }
    
    // $teste = require './site/template.phtml';
  }
  
  public function setTemplate($name): CmThizer {
    $this->template = $name.'.phtml';
    return $this;
  }
  
  public function getTemplate(): string {
    return $this->template;
  }
  
  public function setLandingPage($name): CmThizer {
    $this->landingPage = $name.'.phtml';
    return $this;
  }
  
  public function getLandingPage(): string {
    return $this->landingPage;
  }
  
  public function getUri(): \CmThizer\Uri {
    return $this->uri;
  }
  
  private function resolveParams(): CmThizer {
    $this->params = (array) filter_input_array(INPUT_GET);
    return $this;
  }
  
  private function resolvePost(): CmThizer {
    $this->post = (array) filter_input_array(INPUT_POST);
    return $this;
  }
  
  private function resolveRoutes(): CmThizer {
    
    $site = './site';
    
    /**
     * Nos da a lista de conteudo da pasta site
     * 
     * ## RECURSIVA ##
     */
    function scandirRecursive($dirname): array {
      $items = array();
      if (is_dir($dirname)) {
        foreach (scandir($dirname) as $item) {
          if (!in_array($item, array('.', '..'))) {
            if (is_dir($dirname.DIRECTORY_SEPARATOR.$item)) {
              $items[$dirname.DIRECTORY_SEPARATOR.$item] = scandirRecursive($dirname.DIRECTORY_SEPARATOR.$item);
            } else {
              $items[] = $item;
            }
          }
        }
      }
      return $items;
    }
    $dirItems = scandirRecursive($site);
    
    /**
     * Outra recursiva, agora para organizar os dados da pagina
     * 
     * ## RECURSIVA ##
     */
    function resolve(array $items) {
      $routes = array();
      foreach ($items as $folder => $content) {
        if (is_dir($folder) && in_array('config.json', $content) && in_array('content.md', $content)) {

          $config = json_decode(file_get_contents($folder.'/config.json'), true);
          $config['content'] = $folder.'/content.md';

          $routes[$config['uri']] = $config;
        } else if(is_array($content)) {
          $routes += resolve($content);
        }
      }
      return $routes;
    }
    $this->routes = resolve($dirItems);
    
    return $this;
  }
}

