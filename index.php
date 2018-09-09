<?php
include_once './core/CmThizer.php';

try {
  $cms = new CmThizer();
  
//  $cms->getPlugin('Sitemap')->setActive(false);
  
  $cms->run();
  
} catch (Throwable $e) {
  
  /**
   * With Throwable instance PHP 7 can handle
   * whatever type of exception or error
   */
  render_error_page($e, SHOW_ERRORS);
}

