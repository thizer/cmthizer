<?php

namespace CmThizer\Plugins;

use CmThizer;
use ReflectionMethod;

abstract class AbstractPlugin
{
  const PRE_URI = 1;
  const POS_URI = 2;
  const PRE_PARAMS = 3;
  const POS_PARAMS = 4;
  const PRE_POST = 4;
  const POS_POST = 5;
  const PRE_ROUTES = 6;
  const POS_ROUTES = 7;
  const PRE_RUN = 8;
  const POS_RUN = 9;
  
  private $instance;
  
  private $active = true;
  
  public function setCmThizerInstance(CmThizer $instance) {
    $this->instance = $instance;
  }
  
  protected function getCmThizer(): CmThizer {
    return $this->instance;
  }
  
  public function setActive(bool $status): self {
    $this->active = $status;
    return $this;
  }
  
  public function isActive(): bool {
    return $this->active;
  }
  
  abstract function preUri();

  abstract function posUri();

  abstract function preParams();

  abstract function posParams();

  abstract function prePost();

  abstract function posPost();

  abstract function preRoutes();

  abstract function posRoutes();
  
  abstract function preRun();
  
  abstract function posRun();
  
  // #############################################
  
  /**
   * This method allows to the user to call
   * whatever method in the main CmThizer instance
   * as the same way
   * 
   * @param string $name
   * @param mixed $arguments
   */
  public function __call($name, $arguments) {
    $result = null;
    if (in_array($name, get_class_methods($this->getCmThizer()))) {
      $method = new ReflectionMethod($this->getCmThizer(), $name);
      
      if ($method->isPublic()) {
        $result = $method->invokeArgs($this->getCmThizer(), $arguments);
      }
    }
    return $result;
  }
}
