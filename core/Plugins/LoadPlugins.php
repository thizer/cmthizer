<?php
namespace CmThizer\Plugins;

use InvalidArgumentException;
use CmThizer;

class LoadPlugins
{
  private $cmThizerInstance;
  private $plugins = array();

  public function __construct(array $pluginsPath, CmThizer $cmThizerInstance) {

    $this->cmThizerInstance = $cmThizerInstance;

    foreach ($pluginsPath as $path) {

      $pluginsDir = scandir_recursive($path, true);

      foreach ($pluginsDir as $value) {
        if (is_array($value)) {
          foreach ($value as $subValue) {
            if (!is_array($subValue) && is_file($subValue) && is_readable($subValue) && (pathinfo($subValue, PATHINFO_EXTENSION) == 'php')) {

              $this->append($subValue);
            }
          }
        } else if (is_file($value) && is_readable($value) && (pathinfo($value, PATHINFO_EXTENSION) == 'php')) {

          $this->append($value);
        }

      } // Endforeach
    } // endforeach
  }

  public function append($filename): LoadPlugins {

    require_once $filename;
    $className = basename($filename, '.php');

    $classInstance = new $className();
    if (!$classInstance instanceof AbstractPlugin) {
      throw new \Exception('Plugins must extends to \CmThizer\Plugins\AbstractPlugin');
    }
    $this->plugins[$className] = $classInstance;

    return $this;
  }

  public function dispatch(int $type) {
    foreach($this->plugins as $plugin) {

      if (!$plugin->isActive()) {
        continue;
      }

      $plugin->setCmThizerInstance($this->cmThizerInstance);

      switch ($type) {
        case AbstractPlugin::PRE_URI:
          $plugin->preUri();
          break;
        case AbstractPlugin::POS_URI:
          $plugin->posUri();
          break;
        case AbstractPlugin::PRE_PARAMS:
          $plugin->preParams();
          break;
        case AbstractPlugin::POS_PARAMS:
          $plugin->posParams();
          break;
        case AbstractPlugin::PRE_POST:
          $plugin->prePost();
          break;
        case AbstractPlugin::POS_POST:
          $plugin->posPost();
          break;
        case AbstractPlugin::PRE_ROUTES:
          $plugin->preRoutes();
          break;
        case AbstractPlugin::POS_ROUTES:
          $plugin->posRoutes();
          break;
        case AbstractPlugin::PRE_RUN:
          $plugin->preRun();
          break;
        case AbstractPlugin::POS_RUN:
          $plugin->posRun();
          break;
        default:
          throw new InvalidArgumentException("Unknown plugin dispatch type ($type)");
      }

    } // Endforeach
  }

  public function getAll(): array {
    return $this->plugins;
  }

  public function get(string $name): AbstractPlugin {
    return $this->plugins[$name] ?? null;
  }
}
