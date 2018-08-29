<?php
namespace CmThizer;

class Uri {
  
  private $docRoot;
  
  private $basePath;
  
  private $route;
  
  public function __construct() {
    
    $root = $_SERVER['DOCUMENT_ROOT'];
    $uri = preg_replace("/\?.+/", "", $_SERVER['REQUEST_URI']);
    
    // Split parts of URI
    $uriParts = explode(DIRECTORY_SEPARATOR, trim($uri, DIRECTORY_SEPARATOR));
    $basePath = getenv('REQUEST_SCHEME').'://'.getenv('HTTP_HOST').DIRECTORY_SEPARATOR;
    $route = '';
    
    foreach ($uriParts as $part) {
      if (is_dir($root.DIRECTORY_SEPARATOR.$part)) {
        $root .= DIRECTORY_SEPARATOR.$part;
        $basePath .= $part.DIRECTORY_SEPARATOR;
      } else {
        $route .= DIRECTORY_SEPARATOR.$part;
      }
    }
    
    $this->docRoot = $root;
    $this->basePath = rtrim($basePath, '/');
    $this->route = '/'.trim($route, '/');
  }
  
  public function getDocumentRoot() {
    return $this->docRoot;
  }
  
  public function getBasePath(): string {
    return $this->basePath;
  }
  
  public function getRoute(): string {
    return $this->route;
  }
}
