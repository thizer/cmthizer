<?php
namespace CmThizer;

class Uri {
  
  private $docRoot;
  
  private $basePath;
  
  private $route;
  
  public function __construct() {
    
    $root = getenv('DOCUMENT_ROOT');
    $uri = preg_replace("/\?.+/", "", getenv('REQUEST_URI'));
    
    // Split parts of URI
    $uriParts = explode(DIRECTORY_SEPARATOR, trim($uri, DIRECTORY_SEPARATOR));
    $basePath = '';
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
    $this->basePath = '/'.trim($basePath, '/');
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
