#!/usr/bin/php
<?php
if (PHP_SAPI != 'cli') {
        exit();
}
// includi le librerie
define('DIR_ROOT', dirname(dirname(__FILE__)));
define('DIR_LIB', DIR_ROOT.'/lib');
define('DIR_LOGS', DIR_ROOT.'/logs');
define('DIR_APP', DIR_ROOT.'/app');
define('DIR_CONF', DIR_ROOT.'/config');

/* Importo librerie e funzioni ********************************************** */

require_once DIR_LIB.'/vendor/autoload.php';

$client_http = new GuzzleHttp\Client();

// controlla se c'è file config
if (file_exists(DIR_CONF."/config.json")) {
	echo "file esiste\n";
	// locco il file e lo leggo
	if (rename(DIR_CONF."/config.json",DIR_CONF."/config.json.lock")) {
		$checks = json_decode(file_get_contents(DIR_CONF."/config.json.lock"),true);
	}
} elseif (file_exists(DIR_CONF."/config.json.lock")) {
	echo "checker in esecuzione.... esco\n";
	exit();
}


$now_minute=date(i);
foreach ($checks as $key => $value) {
	if ($now_minute % $value['frequency']!=0){
		//make_check($value);
	}
}
print_r($checks);

// ho finito risalvo il file con i nuovi dati e procedo
if (file_put_contents(DIR_CONF."/config.json", json_encode($checks,JSON_PRETTY_PRINT))) {
	// cancello file di lock
	unlink(DIR_CONF."/config.json.lock");
	echo "file di lock cancellato\n";
} else {
	echo "non sono riuscito a scrivere sul file... avverto e esco....\n";
}
