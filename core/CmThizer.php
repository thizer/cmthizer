<?php
class CmThizer {
  
  private $template = 'template.phtml';
  
  private $landingPage = 'landing-page.phtml';
  
  private $uri;
  
  public function __construct() {
    
    $this->uri = new \CmThizer\Uri();
    $teste = require './site/template.phtml';
  }
  
  public function setTemplate($name): CmThizer {
    $this->template = $name.'.phtml';
    return $this;
  }
  
  public function getTemplate(): string {
    return $this->template;
  }
  
  public function setLandingPage($name): CmThizer {
    $this->landingPage = $name.'.phtml';
    return $this;
  }
  
  public function getLandingPage(): string {
    return $this->landingPage;
  }
  
  public function getUri(): \CmThizer\Uri {
    return $this->uri;
  }
}

