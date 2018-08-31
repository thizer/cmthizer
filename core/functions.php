<?php

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

/**
 * 
 * @param string $dirname
 * @param bool $fullfilename
 * @return array
 */
function scandir_recursive(string $dirname, bool $fullfilename = false): array {
  $items = array();
  if (is_dir($dirname)) {
    foreach (scandir($dirname) as $item) {
      if (!in_array($item, array('.', '..'))) {
        
        // Remove duplicated bars
        $path = preg_replace("/\/{2,}/", '/', $dirname.DIRECTORY_SEPARATOR.$item);
        
        // If is dir scan it too
        if (is_dir($path)) {
          $items[$path] = scandir_recursive($path, $fullfilename);
        } else {
          $items[] = $fullfilename ? $path : $item;
        }
      }
    }
  }
  return $items;
}
