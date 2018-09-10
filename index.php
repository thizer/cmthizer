<?php
include_once './core/CmThizer.php';

try {
  $cms = new CmThizer();
  $cms->step1(); // alias to loadPlugins method
  
//  $cms->getPlugin('MenusPages')->setActive(false);

  $cms->step2(); // alias to dispatchConfigs
  $cms->run();
  
} catch (Throwable $e) {
  
  /**
   * With Throwable instance PHP 7 can handle
   * whatever type of exception or error
   */
  render_error_page($e, SHOW_ERRORS);
}

