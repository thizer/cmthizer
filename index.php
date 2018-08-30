<?php
if (file_exists('vendor/autoload.php')) {
  $loader = include 'vendor/autoload.php';

  // If you want, can add your libraries here with composer
  // $loader->add('System', 'library/.');
} else {
  exit("Project dependencies not found, execute 'php composer.phar install' in the root of project");
}

$cms = new CmThizer();
$cms->run();

