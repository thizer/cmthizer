<?php
namespace CmThizer\Plugins;

use function Composer\Autoload\includeFile;

class LoadPlugins implements \Iterator
{
  private $plugins = array();
  
  public function __construct(string $pluginsPath) {
    
    $pluginsDir = scandir_recursive($pluginsPath, true);
    
    foreach ($pluginsDir as $value) {
      if (is_file($value) && is_readable($value) && (pathinfo($value, PATHINFO_EXTENSION) == 'php')) {
        
        $this->append($value);
        
      } elseif (is_array($value)) {
        foreach ($pluginsDir as $value) {
          if (is_file($value) && is_readable($value) && (pathinfo($value, PATHINFO_EXTENSION) == 'php')) {
            
            $this->append($value);
          }
        }
      }
    }
  }
  
  private function append($filename): LoadPlugins {
    
    require_once $filename;
    $className = basename($filename, '.php');
    
    $classInstance = new $className();
    if ($classInstance instanceof AbstractPlugin) {
      $this->plugins[] = $classInstance;
    }
    return $this;
  }
  
  public function dispatch(int $type): void {
    switch ($type) {
      case AbstractPlugin::PRE_URI:
        
        break;
      case AbstractPlugin::POS_URI:
        
        break;
      case AbstractPlugin::PRE_PARAMS:
        
        break;
      case AbstractPlugin::POS_PARAMS:
        
        break;
      case AbstractPlugin::PRE_POST:
        
        break;
      case AbstractPlugin::POS_POST:
        
        break;
      case AbstractPlugin::PRE_ROUTES:
        
        break;
      case AbstractPlugin::POS_ROUTES:
        
        break;
      case AbstractPlugin::PRE_RUN:
        
        break;
      case AbstractPlugin::POS_RUN:
        
        break;
      default:
        throw new \ErrorException("Unknown plugin dispatch type ($type)");
    }
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
