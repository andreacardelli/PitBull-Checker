<?php 

// Init session
session_start();

/* Constants */

// Env constants
//define('ENV', getenv("APPLICATION_ENV"));
define('ENV', "dev");

// Path constatns
define('DIR_ROOT', dirname(dirname(__FILE__)));
define('DIR_LIB', DIR_ROOT.'/lib');
define('DIR_CHECK', DIR_ROOT.'/checker');
define('DIR_ASSETS', DIR_ROOT.'/assets');
define('PATH_ASSETS', '/pitbull-checker/assets');
define('DIR_CONF', DIR_ROOT.'/config');
define('DIR_CONTROLLERS', DIR_ROOT.'/app/controllers');
define('DIR_VIEWS', DIR_ROOT.'/app/views');
define('LINK_BASE', '/pitbull-checker/app');
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

// map login and logout routes
$router->map('GET','/login', array('controller' => 'authentication', 'action' => 'index', 'view' =>'index'), 'login_index');
$router->map('POST','/login', array('controller' => 'authentication', 'action' => 'login', 'view' =>'index'), 'do_login');
$router->map('GET','/logout', array('controller' => 'authentication', 'action' => 'logout', 'view' =>'logout'), 'do_logout');

// map user routes
$router->map('GET','/user/[:userid]/update', array('controller' => 'user', 'action' => 'settings', 'view' =>'settings'), 'user_settings');
$router->map('POST','/user/[:userid]/update', array('controller' => 'user', 'action' => 'save', 'view' =>'settings'), 'save_user');
$router->map('GET','/user/list', array('controller' => 'user', 'action' => 'list', 'view' =>'list'), 'list_user');
$router->map('GET','/user/create', array('controller' => 'user', 'action' => 'create', 'view' =>'settings'), 'create_user');

// map check routes
$router->map('GET','/list', array('controller' => 'check', 'action' => 'index', 'view' =>'list'), 'list_checker');
$router->map('GET','/edit/[:checkid]', array('controller' => 'check', 'action' => 'edit', 'view' =>'edit'), 'edit_check');
$router->map('POST','/edit/[:checkid]', array('controller' => 'check', 'action' => 'save', 'view' =>'edit'), 'save_check');
$router->map('GET','/create', array('controller' => 'check', 'action' => 'insert', 'view' =>'edit'), 'create_check');

// map dashboard and stats
$router->map('GET','/', array('controller' => 'dashboard', 'action' => 'index', 'view' =>'dashboard'), 'dashboard');
$router->map('GET','/stats', array('controller' => 'stats', 'action' => 'detail', 'view' =>'stats'), 'stats');

// Match current request
$match = $router->match();

// Processing current request: mapping all parameters to variables
$controller = (isset($match['target']['controller']))?$match['target']['controller']:'404';
$action = (isset($match['target']['action']))?$match['target']['action']:'default';
$view = (isset($match['target']['view']))?$match['target']['view']:'404';
$parameters = $match['params'];

// print_r($match);
// exit;

// Check user logged in
if (!isset($_SESSION['user']) && $match['target']['controller'] != 'authentication') {
	header("Location: " . $router->generate('login_index'));
	die();
}

/* Include matched controller */
include (DIR_CONTROLLERS."/$controller.php");	
if(file_exists(DIR_VIEWS . "/$controller/$view.php")) {
	// include head
	// include (DIR_VIEWS . "/inc/head.php");
	include (DIR_VIEWS . "/$controller/$view.php"); // include view (body)
	// include (DIR_VIEWS . "/inc/footer.php");
	// include footer

	// replace di tutte le variabili all'interno
	// butta fuori html elaborato
}

?>