<?php
if (PHP_SAPI != 'cli') {
        exit();
}
date_default_timezone_set('UTC');
// includi le librerie
define('DIR_ROOT', dirname(dirname(__FILE__)));
define('DIR_LIB', DIR_ROOT.'/lib');
define('DIR_LOGS', DIR_ROOT.'/logs');
define('DIR_APP', DIR_ROOT.'/app');
define('DIR_CONF', DIR_ROOT.'/config');

/* Importo librerie e funzioni ********************************************** */
require_once DIR_LIB.'/common_functions.php';
require_once DIR_LIB.'/vendor/autoload.php';

require_once DIR_CONF.'/config.php';

use GuzzleHttp\TransferStats;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

$start = microtime(true);

if (empty($checks = whichToCheck()) || !am_i_online()) exit();

$client = new GuzzleHttp\Client(['http_errors' => false,'allow_redirects' => true,'verify' => false,'connect_timeout' => TIMEOUT,'cookies'=> true,'timeout' => TIMEOUT, 'headers' => ['User-Agent' => 'PitBull Checker/1.0']]);


$promises = (function () use (&$checks,$client) {
    foreach ($checks as $key=>$check) {
        // lock the file now
        lockCheck($check['user'], $check['id']);

        // define options for the Guzzle async request, by default we get the stats of the transfer process
        $options =['on_stats' => function (TransferStats $stats) use ($key,&$checks) {
            $checks[$key]['last_check']=array();
            $checks[$key]['last_check']['stats']=(array)$stats->getHandlerStats();
            $checks[$key]['last_check']['time']=((int)((float)$stats->getTransferTime()*1000));
            $checks[$key]['last_check']['effectiveuri']=$checks[$key]['last_check']['stats']['url'];
            $checks[$key]['all_checks']['count']++;
            $checks[$key]['all_checks']['avg_time']=((($checks[$key]['all_checks']['count']-1)*$checks[$key]['all_checks']['avg_time'])+$checks[$key]['last_check']['time'])/$checks[$key]['all_checks']['count'];
        }];

        // define which method to use to make the request default GET
        $method = (@$check['check']['method'])?$check['check']['method']:'GET';

        // if it is a post let us check if postfields are present
        $form_params= ($method=='POST' && $check['check']['form_params'])?$check['check']['form_params']:'';

        // is this a POST and we have post fields? if yes add to options array all the required key=>values
        if (!empty($form_params)) {
            $options['form_params']=$form_params;
        }

        // do we need basic auth for our GET/POST? if yes add to options array all the required Key=>values
        if (array_key_exists('auth', $check['check']) && isset($check['check']['auth']['username']) && isset($check['check']['auth']['password'])) {
            $options['auth']=[$check['check']['auth']['username'],$check['check']['auth']['password']];
        }        

        // finally let us queue the request for the page/api/form
        yield $client->requestAsync($method, $check['check']['url'],$options);

    }
})(); // Self-invoking anonymous function (PHP 7 only), use call_user_func on older PHP versions.


(new GuzzleHttp\Promise\EachPromise($promises, [
    'concurrency' => CONCURRENCY ,
    'fulfilled' => function (ResponseInterface $response,$index) use (&$checks) {
        $checks[$index]['last_check']['timestamp']=time();
        $checks[$index]['last_check']['statuscode']=$response->getStatusCode();
        $checks[$index]['last_check']['content']=(string) $response->getBody();
        // if ($checks[$index]['check']['method']=='POST') {
        //     $checks[$index]['last_check']['stats']['body']=$checks[$index]['last_check']['content'];
        // }
        $checks[$index]['last_check']['redirect']=0;
        if (($checks[$index]['check']['url']!=$checks[$index]['last_check']['stats']['url'])&& ($checks[$index]['check']['url']."/"!=$checks[$index]['last_check']['stats']['url'])){
            $checks[$index]['last_check']['redirect']=1;
        }
        $healthy = checkHealth($index); // is it healthy?

        alert($checks[$index],$healthy);
        
        log_check($checks[$index],$healthy);

        saveCheck($checks[$index]);
    },
    'rejected' => function ($reason, $index) use (&$checks) {
        // it might be rejectd only for timeouts
        //all HTTP ERRORS ARE NOT TRACKED AS EXCEPTIONS but as regular status codes
        $checks[$index]['last_check']['timestamp']=time();
        $checks[$index]['last_check']['statuscode']='timeout';
        $checks[$index]['last_check']['content']='Timeout reached '.$checks[$index]['last_check']['time'].' ms';
        $checks[$index]['last_check']['message']=$checks[$index]['last_check']['content'];

        $healthy = checkHealth($index); // we know already is not healthy but we need to increment errors
        
        alert($checks[$index],$healthy);
        
        log_check($checks[$index],$healthy);

        saveCheck($checks[$index]);
    }
]))->promise()->wait();

//saveChecks();
unset($checks);
//print_r($checks);

echo date('Y-m-d H:i:s')." - Time taken to perform checks and alerts: ".(microtime(true) - $start).PHP_EOL.PHP_EOL;

