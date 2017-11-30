<?php 
/* Constants */

// Env constants
//define('ENV', getenv("APPLICATION_ENV"));
define('ENV', "dev");

// Path constatns
define('DIR_ROOT', dirname(dirname(__FILE__)));
define('DIR_LIB', DIR_ROOT.'/lib');
define('DIR_LOGS', DIR_ROOT.'/logs');
define('DIR_CHECK', DIR_ROOT.'/checker');
define('DIR_ASSETS', DIR_ROOT.'/assets');
define('PATH_ASSETS', '/pitbull-checker/assets');
define('DIR_CONF', DIR_ROOT.'/config');
define('DIR_CONTROLLERS', DIR_ROOT.'/api/controllers');
define('DIR_VIEWS', DIR_ROOT.'/app/views');
define('LINK_BASE', '/pitbull-checker/api');
// define('LINK_BASE', str_replace('/index.php', '', $_SERVER['PHP_SELF'])); // dynamic

// Set encode and timezone
if(function_exists("mb_internal_encoding")){
	mb_internal_encoding('UTF-8');
}
date_default_timezone_set('Europe/Rome');
// setlocale(LC_ALL, 'it_IT');

/* Import libs and configs */
require_once DIR_LIB.'/vendor/autoload.php';
require_once DIR_LIB.'/common_functions.php';

require_once DIR_CONF.'/config.php';

/* @documentation - http://altorouter.com/ */
$router = new AltoRouter();

// Set our base
$router->setBasePath(LINK_BASE);

// map check routes

$router->map('GET','/logs/success/[:userid]/[:checkid]', array('controller' => 'default', 'action' => 'getsuccess', 'view' =>'none'), 'getsuccess');
$router->map('GET','/logs/failure/[:userid]/[:checkid]', array('controller' => 'default', 'action' => 'getfailure', 'view' =>'none'), 'getfailure');
$router->map('GET','/logs/nosuccess/[:userid]/[:checkid]', array('controller' => 'default', 'action' => 'getnosuccess', 'view' =>'none'), 'getnosuccess');
$router->map('GET','/logs/partial/natural/[:userid]/[:checkid]/[:linestoshow]?', array('controller' => 'default', 'action' => 'partial', 'view' =>'none'), 'getpartial');
$router->map('GET','/logs/partial/reverse/[:userid]/[:checkid]/[:linestoshow]?', array('controller' => 'default', 'action' => 'partialreverse', 'view' =>'none'), 'getpartialreverse');
$router->map('GET','/logs/errors/[:userid]/[:checkid]', array('controller' => 'default', 'action' => 'errors', 'view' =>'none'), 'getpartial_errors');
$router->map('GET','/logs/globalerrors', array('controller' => 'default', 'action' => 'globalerrors', 'view' =>'none'), 'getglobal_errors');

$router->map('GET|POST','/bulk/[:action]/[:userid]/[:value]?', array('controller' => 'bulkactions', 'action' => 'default', 'view' =>'none'), 'bulkaction');

// Match current request
$match = $router->match();

// Processing current request: mapping all parameters to variables
$controller = (isset($match['target']['controller']))?$match['target']['controller']:'default';
$action = (isset($match['target']['action']))?$match['target']['action']:'default';
$view = (isset($match['target']['view']))?$match['target']['view']:'default';
$parameters = $match['params'];



/* Include matched controller */
include (DIR_CONTROLLERS."/$controller.php");	

?>