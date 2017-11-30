<?php
// which checks do i need to perform?
function whichToCheck() {
	$checks=[];
	foreach (glob(DIR_CONF."/checks/*.json") as $filename) {
		$config=json_decode(file_get_contents($filename),true);
        // check if config is not working: if not, print an error and continue
        if(!$config) {
            echo "WARNING - ".date('Y-m-d H:i:s').": $filename is not a valid config\n";
            continue;
        }

        // check if this check is active and frequency is met
		if ((((int)date('i') % (int)$config['frequency'])==0) && $config['status']=='active'){
			// add the new check to the list of checks to perform
            $checks[]=$config;
		}	
	}
	if (empty($checks)) {
		echo date('Y-m-d H:i:s')." - No checks to perform...\n\n";
	} else {
		echo date('Y-m-d H:i:s')." - There are ".count($checks)." checks to perform...\n";
	}
	return $checks;
}
function getUserChecks($userid){
    $checks=[];
    foreach (glob(DIR_CONF."/checks/".$userid.".*") as $filename) {
        $checks[]=json_decode(file_get_contents($filename),true);
    }
    return $checks;
}

function getCheck($userid, $checkid) {
    $filename=DIR_CONF."/checks/".$userid.".".$checkid.".json";
    // rename($filename, $filename.".lock");
    $file = (file_exists($filename)) ? file_get_contents($filename) : "[]";
    return json_decode($file,true);
}

function lockCheck($userid, $checkid) {
    $filename=DIR_CONF."/checks/".$userid.".".$checkid.".json";
    return rename($filename, $filename.".lock");
}


function getAllUsers(){
    $userss=[];
    foreach (glob(DIR_CONF."/users/*.json") as $filename) {
        $users[]=json_decode(file_get_contents($filename),true);
    }
    return $users;
}

function getUser($userid) {
    $filename=DIR_CONF."/users/".$userid.".json";
    // rename($filename, $filename.".lock");
    return json_decode(file_get_contents($filename),true);
}

function saveUser($userdata) {
    $filename=DIR_CONF."/users/".$userdata['user'].".json";
    file_put_contents($filename,json_encode($userdata,JSON_PRETTY_PRINT));
    chmod($filename, 0777);
    // unlink($filename.".lock");
    return true;
}

function saveChecks(){
    global $checks;
    foreach ($checks as $value) {
        $filename = DIR_CONF."/checks/".$value['user'].".".$value['id'].".json";
        file_put_contents($filename, json_encode($value,JSON_PRETTY_PRINT));
        chmod($filename, 0777);
        if(file_exists($filename.'.lock'))
            unlink($filename.".lock");
    }
}
function saveCheck($check){
    $filename = DIR_CONF."/checks/".$check['user'].".".$check['id'].".json";
    file_put_contents($filename, json_encode($check,JSON_PRETTY_PRINT));
    chmod($filename, 0777);
    if(file_exists($filename.'.lock'))
        unlink($filename.".lock");
}
function checkHealth($index) {
    global $checks;
    $rules = $checks[$index]['check']['success_criteria'];
    $success=true;
    $message='';
    foreach ($rules as $key => $value) {
        if (!$success) break; // exit immediately as soon as you get a false
        switch (key($value)) {
            case 'http_response': 
                if ($checks[$index]['last_check']['statuscode']!=(int)$value[key($value)]){
                    $success=$success & false;
                    $message=$message."Status Code: ".$checks[$index]['last_check']['statuscode'];
                }
                break;
            case 'http_response_time':
                if (!($checks[$index]['last_check']['time']<$value[key($value)])){
                    $success=$success & false;
                    $message=$message."Response Time: > ".$value[key($value)];
                }
                break;
            case 'body_contains':
                if (strpos($checks[$index]['last_check']['content'],$value[key($value)]) === false){
                    $success=$success & false;
                    $message=$message."Body does not contains: ".$value[key($value)];
                }
                break;
            case 'body_not_contains':
                if (strpos($checks[$index]['last_check']['content'],$value[key($value)]) !== false){
                    $success=$success & false;
                    $message=$message."Body contains: ".$value[key($value)];
                }
                break;
        }
    }
    if ($success) {
        $success='SUCCESS';
    } else {
        $success="FAILURE";
    }

    if (strlen($checks[$index]['last_check']['content'])>128) {
        $checks[$index]['last_check']['content']="";
    }
    if ($success=='FAILURE') {
        $checks[$index]['check']['errors']++;
        $checks[$index]['check']['last_error']=time();
        if ($checks[$index]['last_check']['statuscode']!='timeout') {
            $checks[$index]['last_check']['message']=$message;
        }   
    } elseif ($success=='SUCCESS') {
        if ($checks[$index]['check']['errors']>=$checks[$index]['max_consecutives_errors']){
            $success='UP_AGAIN';
        }
        $checks[$index]['check']['errors']=0;
        $checks[$index]['last_check']['message']="All rules were met!";
    }

    $checks[$index]['last_check']['status']=$success;
    return $success;
}

function alert($check,$healthy) {
    $user=getUser($check['user']);
    if (($check['check']['errors']==$check['max_consecutives_errors'] && $healthy=='FAILURE') || ($healthy=='UP_AGAIN')) {
        echo $check['name'] ." $healthy -> alerting...\n"; 
        foreach ($check['alert'] as $key => $value) {
            foreach ($value as $recipient) {
                switch ($key) {
                    case 'email':
                            sendMail($recipient,"$healthy ".$check['name'],$check['last_check']['message']."\n<br>Body of answer:\n<br>".$check['last_check']['content'],DEBUG);
                            $user['emails']['sent']++;
                        break;
                    case 'sms':
                        if ($user['sms']['sent']<$user['sms']['credits']) {
                            sendSms(SMS_PROVIDER[SMS_TYPE]['from'],$recipient,"$healthy ".$check['name']." - ".$check['last_check']['message'],DEBUG); // last parameter  optional for sandbox
                            $user['sms']['sent']++;
                        }
                        break;
                    case 'telegram':
                            sendTelegram($user['telegram']['botid'],$recipient,"$healthy ".$check['name'].": ".$check['last_check']['message']."\nBody of answer:\n".$check['last_check']['content'],DEBUG);
                            $user['telegram']['sent']++;
                        break;
                }
                echo "$healthy, sending $key to $recipient for checkid: ".$check['user'].".".$check['id'].".json\n";
            }
        }
    }
    saveuser($user);
}


function log_check($check,$healthy){
    $dir_logs = DIR_LOGS.'/'.$check['user'].'/';
    $filename = $dir_logs.$check['user'].'.'.$check['id'].'.csv';
    $filenameerrors = $dir_logs.'errors/'.$check['user'].'.'.$check['id'].'.csv';
    $globalerrors = DIR_LOGS.'/globalerrors.csv';
    // DEBUG
    //$filename = DIR_LOGS.'/globallog.csv';

    // check if log directory for user exists and if file exists otherwise create it
    if (!file_exists($filename)) {
        if(!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777);
        }
        $row= "date,timestamp,name,status,status_code,response_time,message,content,namelookup_time,connect_time,pretransfer_time,starttransfer_time\n";
        file_put_contents($filename, $row);
        chmod($filename, 0777);
    } 
    // check if error log directory for user exists and if file exists otherwise create it
    if (!file_exists($filenameerrors)) {
        if(!file_exists(dirname($filenameerrors))) {
            mkdir(dirname($filenameerrors), 0777);
        }
        $row= "date,timestamp,name,status,status_code,response_time,message,content,namelookup_time,connect_time,pretransfer_time,starttransfer_time\n";
        file_put_contents($filenameerrors, $row);
        chmod($filenameerrors, 0777);
    } 
    // check if gloabl error log file exists otherwise create it
    if (!file_exists($globalerrors)) {
        $row= "user,id,date,timestamp,name,status,status_code,response_time,message,content,namelookup_time,connect_time,pretransfer_time,starttransfer_time\n";
        file_put_contents($globalerrors, $row);
        chmod($globalerrors, 0777);
    } 
    $namelookup_time=(int)($check['last_check']['stats']['namelookup_time']*1000);
    $connect_time=(int)($check['last_check']['stats']['connect_time']*1000);
    $pretransfer_time=(int)($check['last_check']['stats']['pretransfer_time']*1000);
    $starttransfer_time=(int)($check['last_check']['stats']['starttransfer_time']*1000);
    $row = '"'.date("Y-m-d H:i:s").'",'.time().',"'.$check['name'].'","'.$healthy.'",'.$check['last_check']['statuscode'].','.$check['last_check']['time'].',"'.$check['last_check']['message'].'","'.$check['last_check']['content'].'",'.$namelookup_time.','.$connect_time.','.$pretransfer_time.','.$starttransfer_time."\n";
    file_put_contents($filename, $row,FILE_APPEND);
    if ($healthy!='SUCCESS') {
        file_put_contents($filenameerrors, $row,FILE_APPEND);
        file_put_contents($globalerrors, '"'.$check['user'].'","'.$check['id'].'",'.$row,FILE_APPEND);
    }
}

function returnLogs($filename,$type='ALL',$order='natural',$linesToShow='ALL'){    
    if (is_null($linesToShow)) {$linesToShow='ALL';}
    if ($order=='natural' && $linesToShow=='ALL') {
        $fh = fopen($filename, 'r') or die("file not found");
        while (!feof($fh)) {
            $line = fgets($fh, 4096);
            if ($type!='ALL') {
                if (preg_match($type, $line)) { echo $line; }
            } else {
                echo $line;
            }
        }
    } else {
        if ($linesToShow=='ALL') {$linesToShow=43200;}
        $lines = array();
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        unset($lines[0]);
        // print_r($lines);
        // exit();
        // $counter = 0;
        // $pos = -2;
        // while ($counter <= (int)$linesToShow && -1 !== fseek($fh, $pos, SEEK_END)) {
        //    $char = fgetc($fh);
        //     if (PHP_EOL == $char) {
        //             $lines[] = $currentLine;
        //             $counter++;
        //             $currentLine = '';
        //     } else {
        //             $currentLine = $char . $currentLine;
        //     }
        //     $pos--;
        // }
        // if ($order=='natural') { // we need to reverse the array because reading from end of file returns reverse by default
        //     $lines=array_reverse($lines);
        // }
        if ($order=='natural') { // we need to reverse the array because reading from end of file returns reverse by default
            $lines=array_reverse($lines);
        }

        $lines = array_reverse(array_slice($lines, 0, $linesToShow));

        foreach ($lines as $line) {
            echo $line."\n";
        }
        
    }
    if($fh)
        fclose($fh);
}

function sendSms($from,$to,$text,$sandbox=false){
    if ($sandbox) return;
    call_user_func(SMS_TYPE, $from,$to,$text); // your own function must be written in the main /config/config.php
}

function sendTelegram($botId,$chatId,$message,$sandbox=false){
    if ($sandbox) return;
    $url = 'https://api.telegram.org/bot' . $botId . '/sendMessage?text='.urlencode($message).'&chat_id='.$chatId;
    $result = file_get_contents($url);
}

function sendMail($to,$subject,$message,$sandbox=false){
    if ($sandbox) return;
    
    $templatehtml =file_get_contents(DIR_CONF.'/emailtemplate.html');
    $link = WEBSITE.'app/list';

    // replace variables in templatehtml 
    $templatehtml = preg_replace(['/{url}/','/{message}/'], [$link,$message], $templatehtml);

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);          // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = SMTP_SERVER;                            // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = SMTP_USER;                          // SMTP username
        $mail->Password = SMTP_PASSWORD;                      // SMTP password
        $mail->SMTPSecure = SMTP_SSL;                         // Enable TLS encryption, `ssl` also accepted
        $mail->Port = SMTP_PORT;                              // TCP port to connect to

        //Recipients
        $mail->setFrom(SMTP_MAIL_FROM_ADDRESS,SMTP_MAIL_FROM_NAME);
        $mail->addAddress($to);     // Add a recipient

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $templatehtml;
        $mail->AltBody = $message;

        $mail->send();
        //echo 'Alert message has been sent to: '.$to.' for error: '.$subject.'\n';
    } catch (Exception $e) {
        //echo 'Alert message could not be sent to '.$to.' for error: '.$subject.'\n';
        //echo 'Mailer Error: ' . $mail->ErrorInfo."\n";
    }
}

function encrypt($data) {
    $l = strlen(CRYPT_KEY);
    if ($l < 16)
        $key = str_repeat(CRYPT_KEY, ceil(16/$l));
    if ($m = strlen($data)%8)
        $data .= str_repeat("\x00",  8 - $m);
    if (function_exists('mcrypt_encrypt'))
        $val = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_ECB);
    else
        $val = openssl_encrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
    return $val;
}

function decrypt($data) {
    $l = strlen(CRYPT_KEY);
    if ($l < 16)
        $key = str_repeat(CRYPT_KEY, ceil(16/$l));
    if (function_exists('mcrypt_encrypt'))
        $val = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_ECB);
    else
        $val = openssl_decrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
    return $val;
}

function remove_outliers($array) {
   if(count($array) == 0) {
     return $array;
   }
   $ret = array();
   $mean = array_sum($array)/count($array);
   $stddev = stats_standard_deviation($array);
   $outlier = 3 * $stddev;
   foreach($array as $a) {
       if(!abs($a - $mean) > $outlier) {
           $ret[] = $a;
       }
   }
   return $ret;
}

function am_i_online() {
    $retval = 0;
    system("ping -c 1 -q -w 1 8.8.8.8 > /dev/null", $retval);
    if (!($retval == 0)) {echo date('Y-m-d H:i:s')." - Internet connection is not available".PHP_EOL;}
    return $retval == 0;
}
