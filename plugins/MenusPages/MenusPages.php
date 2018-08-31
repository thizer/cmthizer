<?php
use CmThizer\Plugins\AbstractPlugin;

class MenusPages extends AbstractPlugin {
  
  public function preUri(): void {}
  public function posUri(): void {}
  
  public function preParams(): void {}
  public function posParams(): void {}
  
  public function prePost(): void {}
  public function posPost(): void {}

  public function preRoutes(): void {}
  public function posRoutes(): void {}

  public function preRun(): void {
//     dump(array(
//       get_class_methods($this->getCmThizer()->getUri()),
//       $this->getCmThizer()->getUri()
//     ));
  }
  
  public function posRun(): void {}
}