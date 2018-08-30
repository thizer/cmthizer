<?php
session_start();

// Sao Paulo Timezone (Brazil - Choose yours)
date_default_timezone_set('America/Sao_Paulo');

// Constants configs setted in VirtualHost (we will get there)
define('DEVELOPMENT', (bool) getenv("DEVELOPMENT"));
define('SHOW_ERRORS', (bool) getenv("SHOW_ERRORS"));
if (SHOW_ERRORS) {
	error_reporting(E_ALL);
	ini_set('display_errors', true);
}

// Root path
// Everything is related to the root path
chdir(dirname(__DIR__));
