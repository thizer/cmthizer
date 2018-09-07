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

function in_array_any(array $needle, array $target): bool {
  // Remove arrays from needle
  foreach ($needle as $k => $v) {
    if (is_array($v)) {
      unset($needle[$k]);
    }
  }
  
  // Remove arrays from target
  foreach ($target as $k => $v) {
    if (is_array($v)) {
      unset($target[$k]);
    }
  }
  
  return !empty(array_intersect($needle, $target));
}

function render_error_page(Throwable $exception, $showErrors = false, $errorFile = 'assets/error.phtml') {
  
  $validCodes = array(
    "100","101","200","201","202","203","204","205","206",
    "300","301","302","303","304","305","306","307",
    "400","401","402","403","404","405","406","407","408","409","410","411","412","413","414","415","416","417",
    "500","501","502","503","504","505"
  );
  
  if (in_array($exception->getCode(), $validCodes)) {
    http_response_code($exception->getCode());
  }
  
  if (file_exists($errorFile) && is_readable($errorFile)) {
    
    // The uri object resolve url with localhost, virtualhost, etc...
    $uri = new \CmThizer\Uri();
    
    include $errorFile;
  } else if ($showErrors) {
    echo $exception->getCode().' - '.$exception->getMessage();
    dump($exception);
  } else {
    exit('Error nº'.$exception->getCode().' - Something went wrong');
  }
  
}

/**
 * Translate a throwable getTrance to a String
 * !!! CUIDADO !!! funcao recursiva....
 *
 * @param mixed $args
 * @param bool $root
 * @return string
 */
function getTraceArgsAsString($args, bool $root = true) {
  $argString = "";
  
  switch (gettype($args)) {
    case 'string':
      $argString .= '"' . $args . '"';
      break;
    case 'integer':
    case 'float':
    case 'double':
      $argString .= '(' . gettype($args) . ') ' . $args;
      break;
    case 'boolean':
      $argString .= ($args ? 'true' : 'false');
      break;
    case 'array':
      if ($root) {
        foreach ($args as $key => $arg) {
          $argString .= getTraceArgsAsString($arg, false) . ", ";
        }
        $argString = preg_replace("/,(\s)?$/", "", $argString);
      } else {
        foreach ($args as $key => $arg) {
          $argString .= '"' . $key . '" => ' . getTraceArgsAsString($arg, false) . ", ";
        }
        $argString = "array(" . preg_replace("/,(\s)?$/", "", $argString) . ")";
      }
      break;
    case 'NULL':
      $argString .= "NULL";
      break;
    case 'object':
      $argString .= ($args == null) ? "NULL" : get_class($args);
      break;
    default:
      // O proprio type
      $argString .= gettype($args);
  }
  return $argString;
}

function str_maxlen(string $str, int $maxLen) {
  if (strlen($str) > $maxLen) {
    $str = substr($str, 0, ($maxLen-1)).'&#133;';
  }
  return $str;
}









