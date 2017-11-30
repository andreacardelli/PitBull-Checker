<?php

$action = $parameters['action'];
$value = (isset($parameters['value'])) ? $parameters['value'] : '';
$user = $parameters['userid'];

// echo "I have to $action all checks owned by $user...";

$response = array('error' => 'No checks');

if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
	die("No auth");
}

// TODO: check that user for which we want to make bulk operations is logged and authorized
if(isset($_POST['all'])) {
	$filenames = glob(DIR_CONF."/checks/".$user."*.json");
}elseif(isset($_POST['checks']) && !empty($_POST['checks'])){
	$filenames = array();
	foreach ($_POST['checks'] as $id) {
		$filenames[] = DIR_CONF.'/checks/'.$user.'.'.$id.'.json';
	}
}

foreach ($filenames as $filename) {
	$check=json_decode(file_get_contents($filename),true);
    // choose the bulk action to perform
	switch ($action) {
		case 'enable':
			$check['status']='active';
			$response = updateFile($filename, $check);
			// file_put_contents($filename,json_encode($check,JSON_PRETTY_PRINT));
			break;
		case 'disable':
			$check['status']='disabled';
			$response = updateFile($filename, $check);
			// file_put_contents($filename,json_encode($check,JSON_PRETTY_PRINT));
			break;
		case 'changefrequency':
			if (!empty($value)) {
				$check['frequency']=(int)$value;
				$response = updateFile($filename, $check);
				// file_put_contents($filename,json_encode($check,JSON_PRETTY_PRINT));
			}
			break;
		case 'changemaxerrors':
			if (!empty($value)) {
				$check['max_consecutives_errors']=(int)$value;
				$response = updateFile($filename, $check);
				// file_put_contents($filename,json_encode($check,JSON_PRETTY_PRINT));
			}
			break;
		case 'reseterrors':
			if (!empty($value)) {
				$check['check']['errors']=0;
				$response = updateFile($filename, $check);
				// file_put_contents($filename,json_encode($check,JSON_PRETTY_PRINT));
			}
			break;
		default:
				$response = array('error' => "Action is not allowed");
			break;
	}	
}

// $response = array('success' => 'OK');
header('Content-type: application/json');
echo json_encode($response);

function updateFile($filename, $check) {
	$response = array('success' => true);
	try {
		$byteInserted = file_put_contents($filename,json_encode($check,JSON_PRETTY_PRINT));
		$response = ($byteInserted == false) ? array('error' => 'Can\'t write on file: ' . $filename) : $response;
	} catch (Exception $e) {
		$response = array('error' => $e->getMessage());
	}
	return $response;
}