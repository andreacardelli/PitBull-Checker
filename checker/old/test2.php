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

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;


$start = microtime(true);

// Initialize the Client
$client = new Client();

// The `projects` array contains the code of 60 Arduino projects
$usernames=array('1'=>'1','2'=>'2','3'=>'3','4'=>'2','5'=>'1');
$requests = [];
// Gather the requests for all provided projects
foreach ($usernames as $key=>$username) {
    // create the body of the request
    // get the project files & their contents as a JSON string
    $requests[$key] = new Request('GET', 'http://www.cardelli.info/testparrallel.php?wait='.$username);
}

// Perform the actual requests
$responses = Pool::batch($client, $requests, 
	[
	'concurrency' => 10,
	'options'=>['on_stats' => function (TransferStats $stats) use ($requests) {
		echo key($requests)."\n";
        echo $stats->getEffectiveUri() . "\n";
        echo $stats->getTransferTime() . "\n";
    	}]
     ]);

foreach ($responses as $key=>$response) {
    print_r($key ." - ".$response->getStatusCode(). " - ".$response->getBody()->getContents().PHP_EOL);
}
echo microtime(true) - $start.PHP_EOL;

