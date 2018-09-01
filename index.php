<?php
include_once './core/CmThizer.php';

$cms = new CmThizer();

//$cms->getPlugin('MenusPages')->setActive(false);
$cms->run();

