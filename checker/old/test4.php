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

use GuzzleHttp\TransferStats;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

$start = microtime(true);

$client = new GuzzleHttp\Client(['timeout' => 12.0]); // see how i set a timeout

//
$requestPromises = [];
$sitesArray = ['cardelli1'=>'http://www.cardelli.info/testparrallel.php?wait=1','cardelli5'=>'http://www.cardelli.info/testparrallel.php?wait=5','cardelli6'=>'http://www.cardelli.info/testparrallel.php?wait=6'];

foreach($sitesArray as $key=>$site)
{
    $requestPromises[$key] = $client->getAsync($site);
}

$results = GuzzleHttp\Promise\settle($requestPromises)->wait();

foreach($results as $domain => $result)
{
    $site=[];
    //$this->logger->info('Crawler FetchHomePages: domain check ' . $domain);

    if($result['state'] === 'fulfilled')
    {
        $response = $result['value'];
        if($response->getStatusCode() == 200)
        {
            $site[$domain]['html']=$response->getBody()->getContents();
        }
        else{
            $site[$domain]['html']=$response->getStatusCode();
        }
    }
    else if($result['state'] === 'rejected'){ // notice that if call fails guzzle returns is as state rejected with a reason.

        $site[$domain]['html']='ERR: ' . $result['reason'] ;
    }
    else{
        $site[$domain]['html']='ERR: unknown exception ' ;
        //$this->logger->err('Crawler FetchHomePages: unknown fetch fail domain: ' . $domain  );
    }

    //$this->entityManager->persist($site); // this is a call to Doctrines entity manager
    print_r($site);
}
$time_elapsed_secs = microtime(true) - $start;
echo $time_elapsed_secs.PHP_EOL;
