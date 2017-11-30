<?php
DEFINE('DEBUG',true); // if debug false then SEND alert otherwise do not send really
// system defined limits for Guzzle
define('TIMEOUT',20); // seconds after that we can consider the check failed
define('CONCURRENCY',10); // max concurrent connections, it really depends on your system

// SUPER SECRET KEY to encrypt and decrypt json files (checks and users) TO BE DONE
define('CRYPT_KEY',"mannaggialapeppona");

// define domain and directory of install for email links
define('WEBSITE',"http://www.aperion.info/pitbull-checker/");

// mail configuration for alerting system wide - not per user
define('SMTP_SERVER','smtp_server');
define('SMTP_USER','smtp_user');
define('SMTP_PASSWORD','smtp_password');
define('SMTP_SSL','tls'); // if needed
define('SMTP_PORT',587); // if needed
define('SMTP_MAIL_FROM_ADDRESS',"youremail@yourdomain.com");
define('SMTP_MAIL_FROM_NAME',"PitBull Checker");

// SMS configuration for alerting for basic auth - system wide - not per user - add more define if you need it for your provider
define('SMS_TYPE','sendInfobip'); // must match the name of the function below

const SMS_PROVIDER = [
	'sendInfobip'=>['endpoint'=>'https://api.infobip.com/sms/1/text/single','user'=>'yourusername','password'=>'yourpassword','from'=>'yourfrom13chars'],
	'sendYOURPROVIDER'=>['endpoint'=>'https://yoursmsproviderendpoint/','user'=>'yourusername','password'=>'yourpassword','from'=>'yourfrom13chars']
];

// Telegram BOT configuration is per user and not system wide
define('TELEGRAM_BOTID','telegram_botid');


// write your personal function to send SMS depending on your SMS provider Twilio, Bulksms, plivo, smsapi,....
// it will receive $from,$to,$text in this order $to will be in the form +countrycodeprefixnumber as string
function sendInfobip($from,$to,$text){
	// prepare message
	$data_string=json_encode(array('from'=>$from,'to'=>$to,'text'=>$text));

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,SMS_PROVIDER[SMS_TYPE]['endpoint']);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
    	"Authorization: Basic " . base64_encode(SMS_PROVIDER[SMS_TYPE]['user'] . ":" . SMS_PROVIDER[SMS_TYPE]['password']),                                                                         
    	'Content-Type: application/json',                                                                                
    	'Content-Length: ' . strlen($data_string))                                                                       
		); 
	$result=json_decode(curl_exec ($ch),true);
	$result=$result['messages'][0];
	curl_close ($ch);
	//echo "Message to: ".$result['to']. " sent succesfully with id: ".$result['messageId']."\n";
}

function sendYOURPROVIDER($from,$to,$text) {
	$url = SMS_PROVIDER[SMS_TYPE]['endpoint']."?user=".SMS_PROVIDER[SMS_TYPE]['user']."&pwd=".SMS_PROVIDER[SMS_TYPE]['password']."&sadr=".$from."&dadr=".$to."&text=".urlencode($text);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$result=curl_exec ($ch);
	curl_close ($ch);
}

