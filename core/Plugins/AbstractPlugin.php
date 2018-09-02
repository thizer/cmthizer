<?php

namespace CmThizer\Plugins;

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
  const BEFORE_RENDER = 10;
  
  private $instance;
  
  private $active = true;
  
  public function setCmThizerInstance(\CmThizer $instance): void {
    $this->instance = $instance;
  }
  
  protected function getCmThizer(): \CmThizer {
    return $this->instance;
  }
  
  public function setActive(bool $status): self {
    $this->active = $status;
    return $this;
  }
  
  public function isActive(): bool {
    return $this->active;
  }
  
  abstract function preUri(): void;

  abstract function posUri(): void;

  abstract function preParams(): void;

  abstract function posParams(): void;

  abstract function prePost(): void;

  abstract function posPost(): void;

  abstract function preRoutes(): void;

  abstract function posRoutes(): void;
  
  abstract function preRun(): void;
  
  abstract function posRun(): void;
  
  // #############################################
  
  public function baseUrl(string $link): string {
    return $this->instance->url($link);
  }
  
}
