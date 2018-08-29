<?php
namespace CmThizer;

class Uri {
  
  private $docRoot;
  
  private $basePath;
  
  private $slug;
  
  public function __construct() {
    
    $root = $_SERVER['DOCUMENT_ROOT'];
    $uri = preg_replace("/\?.+/", "", $_SERVER['REQUEST_URI']);
    
    // Split parts of URI
    $uriParts = explode(DIRECTORY_SEPARATOR, trim($uri, DIRECTORY_SEPARATOR));
    $basePath = getenv('REQUEST_SCHEME').'://'.getenv('HTTP_HOST').DIRECTORY_SEPARATOR;
    $slug = '';
    
    foreach ($uriParts as $part) {
      if (is_dir($root.DIRECTORY_SEPARATOR.$part)) {
        $root .= DIRECTORY_SEPARATOR.$part;
        $basePath .= $part.DIRECTORY_SEPARATOR;
      } else {
        $slug .= DIRECTORY_SEPARATOR.$part;
      }
    }
    
    $this->docRoot = $root;
    $this->basePath = $basePath;
    $this->slug = trim($slug, '/');
  }
  
  public function getDocumentRoot() {
    return $this->docRoot;
  }
  
  public function getBasePath(): string {
    return $this->basePath;
  }
  
  public function getSlug(): string {
    return $this->slug;
  }
}
