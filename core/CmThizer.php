<?php
class CmThizer {
  
  private $template = 'template.phtml';
  
  private $landingPage = 'landing-page.phtml';
  
  private $uri;
  
  private $params = array();
  
  private $post = array();
  
  public function __construct() {
    try {
      
      $this->uri = new \CmThizer\Uri();
      
      $this->resolveParams();
      $this->resolvePost();
      $this->resolveRoutes();
      
    } catch (Exception $ex) {
      dump($ex);
    }
    
    // $teste = require './site/template.phtml';
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
  
  private function resolveParams(): CmThizer {
    $this->params = (array) filter_input_array(INPUT_GET);
    return $this;
  }
  
  private function resolvePost(): CmThizer {
    $this->post = (array) filter_input_array(INPUT_POST);
    return $this;
  }
  
  private function resolveRoutes() {
    
    dump(scandir('./site'));
    
  }
}

