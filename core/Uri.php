<?php
namespace CmThizer;

class Uri {
  
  private $docRoot;
  
  private $basePath;
  
  public function __construct() {
    
    $root = $_SERVER['DOCUMENT_ROOT'];
    $uri = $_SERVER['REQUEST_URI'];
    
    // Split parts of URI
    $uriParts = explode(DIRECTORY_SEPARATOR, trim($uri, DIRECTORY_SEPARATOR));
    $basePath = DIRECTORY_SEPARATOR;
    
    foreach ($uriParts as $part) {
      if (is_dir($root.DIRECTORY_SEPARATOR.$part)) {
        $root .= DIRECTORY_SEPARATOR.$part;
        $basePath .= $part.DIRECTORY_SEPARATOR;
      } else {
        break;
      }
    }
    
    $this->docRoot = $root;
    $this->basePath = $basePath;
  }
  
  public function getDocumentRoot() {
    return $this->docRoot;
  }
  
  public function getBasePath(): string {
    return $this->basePath;
  }
  
}