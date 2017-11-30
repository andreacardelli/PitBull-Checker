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


echo date('Y-m-d H:i:s').PHP_EOL;
$year = date('Y');
$nowweek= date('W');
$now = (int)$year.$nowweek;

$dirs = array_filter(glob(DIR_LOGS.'/*'), 'is_dir');
foreach ($dirs as $dir) {
	foreach (glob($dir.'/*') as $filename) {
		// first move log to a new name so if someone adds lines nothing happens
		copy($filename,$filename.'.lock');
		// work on new file
		$filename= $filename.'.lock';
		$basedirforlog = dirname($filename).'/historical/';
		$basefilename = basename($filename);
		echo "Processo... ".$basefilename."\n";
		$fh = fopen($filename, 'r') or die("file not found");
		while (!feof($fh)) {
		    $line = fgets($fh, 4096);
		    $csvline=explode(',', $line);
		    $lineanum= (int)date('YW',$csvline[1]);
		    
		    $tmpfilename = $basedirforlog. date('Y/W',$csvline[1]).'/'.$basefilename;
		    if ($lineanum>197001 && !file_exists($tmpfilename.'.gz') && $lineanum!=$now){
		    	// write new weekly files for the past weeks and if gz file are not already present
				force_file_put_contents($tmpfilename,$line);
			} elseif ($lineanum==$now) {
				// actual week will be the new csv content
				force_file_put_contents($filename.'.tmp',$line);
			}
		}
		// adesso gzippiamo il file
		//gzencode(file_get_contents($file_name));
		break;
	}
	break;
}
$dirs = array_filter(glob(DIR_LOGS.'/*'), 'is_dir');
foreach ($dirs as $dir) {
	echo $dir.PHP_EOL;
	if (is_dir($dir.'/historical')){
		$files= getDirContents($dir.'/historical/');
	}
}
foreach ($files as $filename) {
	$fileparts=explode('/',$filename);
	$num=$fileparts[count($fileparts)-3].$fileparts[count($fileparts)-2];
	if ((int)$num<(int)$now && !file_exists($filename.'.gz')) {
		echo "comprimo....$num \n";
		$fp = gzopen ($filename.'.gz', 'w9');
		// Compress the file
		gzwrite ($fp, file_get_contents($filename));
		// Close the gz file and we're done
		gzclose($fp);	
	} 
}

function force_file_put_contents($filename,$content){
	$dir = dirname($filename);
	if (!is_dir($dir)) {
	  // dir doesn't exist, make it recursive
	  mkdir($dir, 0777, true);
	}
	file_put_contents($filename, $content,FILE_APPEND);
}

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
        	if (pathinfo($path)['extension']=='csv')
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            //$results[] = $path;
        }
    }

    return $results;
}