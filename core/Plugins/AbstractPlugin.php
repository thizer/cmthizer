<?php

namespace CmThizer\Plugins;

abstract class AbstractPlugin
{
  const PRE_URI = 1;
  const POS_URI = 2;
  const PRE_PARAMS = 3;
  const POS_PARAMS = 4;
  const PRE_POST = 4;
  const POS_POST = 5;
  const PRE_ROUTES = 6;
  const POS_ROUTES = 7;
  const PRE_RUN = 8;
  const POS_RUN = 9;
  
  abstract function preUri();

  abstract function posUri();

  abstract function preParams();

  abstract function posParams();

  abstract function prePost();

  abstract function posPost();

  abstract function preRoutes();

  abstract function posRoutes();
  
  abstract function preRun();
  
  abstract function posRun();
}
