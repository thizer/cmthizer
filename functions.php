<?php

if (!function_exists('dump')) {

  /**
   * Simple debug function, just like Zend\Debug.
   *
   * @param mixed $var What you want to debug.
   * @param bool $echo If false, this function will return the result.
   * @return string
   */
  function dump($var, $echo = true)
  {
    ob_start();
    var_dump($var);

    /**
     * $argv when you run this function by command line.
     */
    if (isset($argv)) {
      $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", ob_get_clean()) . "\n\n";
    } else {
      $output = "<pre>" . preg_replace("/\]\=\>\n(\s+)/m", "] => ", ob_get_clean()) . "</pre>";
    }
    if ($echo) {
      echo $output;
      exit;
    }
    return $output;
  }
}