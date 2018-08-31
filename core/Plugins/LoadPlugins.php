<?php
namespace CmThizer\Plugins;

class LoadPlugins implements \Iterator
{
  private $plugins = array();
  
  public function __construct(string $path) {
    if (is_dir($path) && is_readable($path)) {
      foreach (scandir($path) as $item) {
        if (is_file($path.'/'.$item) && (preg_replace("/.+\./", '', $item) == 'php')) {
          $classname = $path.'/'.$item;
          $instance = new $classname();
          
          if ($instance instanceof AbstractPlugin) {
            $this->plugins[] = $instance;
          }
        }
      }
    }
  }
  
  public function dispatch(int $type) {
    
  }

  public function current() {
    
  }

  public function key(): \scalar {
    
  }

  public function next(): void {
    
  }

  public function rewind(): void {
    
  }

  public function valid(): bool {
    
  }

}
