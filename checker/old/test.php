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


$usernames=array('cardelli'=>'andreacardelli','doc'=>'doctrine','hyp'=>'hyperoslo','koh'=>'kohana');


use Amp\Artax\Response;
use Amp\Loop;

Loop::run(function () use ($usernames) {
	foreach ($usernames as  $key=>$value) {
		$uris[$key]='https://api.github.com/users/'.$value;
	}
    // $uris = [
    //     "https://google.com/",
    //     "https://github.com/",
    //     "https://stackoverflow.com/",
    // ];

    // Instantiate the HTTP client
    $client = new Amp\Artax\DefaultClient;
    $requestHandler = function (string $uri) use ($client) {
        /** @var Response $response */
        $response = yield $client->request($uri);
        return ['headers'=>$response->getHeaders(),'body'=>$response->getBody()];
    };
    try {
        $promises = [];
        foreach ($uris as $key=>$uri) {
            $promises[$key] = Amp\call($requestHandler, $uri);
        }
        $bodies = yield $promises;
        foreach ($bodies as $uri => $body) {
            //print $uri . " - " . \strlen($body) . " bytes" . PHP_EOL;
            echo $uri ." - " . $body['headers']['status'][0]. PHP_EOL;
        }
    } catch (Amp\Artax\HttpException $error) {
        // If something goes wrong Amp will throw the exception where the promise was yielded.
        // The Client::request() method itself will never throw directly, but returns a promise.
        echo $error;
    }
});