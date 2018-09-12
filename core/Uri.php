<?php
namespace CmThizer;

class Uri {
  
  private $docRoot;
  
  private $basePath;
  
  private $route;
  
  public function __construct() {
    
    $root = getenv('DOCUMENT_ROOT');
    $uri = preg_replace("/\?.+/", "", getenv('REQUEST_URI'));
    
    // @TODO: DIRECTORY_SEPARATOR is different with Windows and Others
    
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
  
  public function getDocumentRoot(): string {
    return $this->docRoot;
  }
  
  public function getBasePath(): string {
    return $this->basePath;
  }
  
  public function getRouteName(): string {
    return $this->route;
  }
  
  /**
   * 
   * @param string $link
   * @return string
   */
  public function getUrl(?string $link = ''): string {
    $url = getenv('REQUEST_SCHEME').'://'.getenv('HTTP_HOST');
    
    $href = preg_replace("/\/{2,}/", '/', $this->getBasePath().'/'.$link);
    
    return $url.'/'.trim($href, '/');
  }
  
  public function url(string $link = ''): string {
    return $this->getUrl($link);
  }
  
  public function getBaseUrl(string $link = ''): string {
    return $this->getUrl($link);
  }
  
  public function baseUrl(string $link = ''): string {
    return $this->getUrl($link);
  }
}
