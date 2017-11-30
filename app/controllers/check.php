<?php

$user = getUser($_SESSION['user']);
$userChannels = array();

$availableChannels = array('emails' => array('recipients', 'email'), 'telegram' => array('chatids'), 'sms' => array('numbers'));

foreach ($availableChannels as $channel => $keyAddress) {
	if(isset($user[$channel][$keyAddress[0]])){
		foreach ($user[$channel][$keyAddress[0]] as $address) {
			if(is_array($address))
				$userChannels[$channel][] = $address[end($keyAddress)];
			else
				$userChannels[$channel][] = $address;
		}
	}
}

// reformat emails to email
// da uniformare
$userChannels['email'] = $userChannels['emails'];
unset($userChannels['emails']);

/* variable list */
$statusSimbol = array(
		'UP_AGAIN' => '!',
		'UNKNOWN' => '?',
		'FAILURE' => '<i class="glyphicon glyphicon-arrow-down"></i>',
		'SUCCESS' => '<i class="glyphicon glyphicon-arrow-up"></i>'
	);
$statusIcon = array(
		'active' => array('label' => 'The check is running', 'icon' => 'refresh'),
		'disabled' => array('label' => 'The check is not running', 'icon' => 'off'),
	);

switch ($action) {
	case 'index':
		$checks = getUserChecks($_SESSION['user']);
	break;
	case 'insert':

		$check = array(
				'id' => uniqid(),
				'status' => 'disabled',
				'user' => $_SESSION['user'],
				'max_consecutives_errors' => 3,
				'frequency' => 10,
				'alert' => array(
						'email' => array(),
						'telegram' => array(),
						'sms' => array()
					),
				'check' => array(
						'form_params' => array('' => ''),
						'success_criteria' => array(array('action' => ''))
					)
			);

	break;
	case 'edit':

		if(!empty($parameters['checkid'])){
			$check = getCheck($_SESSION['user'], $parameters['checkid']);
			if(!isset($check['alert']) || empty($check['alert'])) {
				$check['alert'] = array(
						'email' => array(),
						'sms' => array(),
						'telegram' => array(),
					);
			}
			if(!isset($check['check']['form_params']) || empty($check['check']['form_params'])) {
				$check['check']['form_params'] = array('' => '');
			}
		}else{
			$response = array('error' => 'No checkid');
		}

	break;
	case 'save':

		if(!empty($parameters['checkid'])) {
			$checkSaved = getCheck($_SESSION['user'], $parameters['checkid']);
			// Filter empty key
			$checkData = array_filter($_POST);

			if(!isset($checkData['status']) || empty($checkData['status'])) {
				$checkData['status'] = 'disabled';
			}

			if(isset($checkSaved['last_check'])) {
				$checkData['last_check'] = $checkSaved['last_check'];
			}

			if(isset($checkSaved['all_checks'])) {
				$checkData['all_checks'] = $checkSaved['all_checks'];
			}

			if(isset($checkSaved['check']['errors'])) {
				$checkData['check']['errors'] = $checkSaved['check']['errors'];
				$checkData['check']['last_error'] = $checkSaved['check']['last_error'];
			}

			if(isset($checkData['frequency'])) {
				$checkData['frequency'] = (int)$checkData['frequency'];
			}

			if(!isset($checkData['status'])) {
				$checkData['status'] = 'disabled';
			}

			if(isset($checkData['max_consecutives_errors'])) {
				$checkData['max_consecutives_errors'] = (int)$checkData['max_consecutives_errors'];
			}

			if(isset($checkData['check']['success_criteria'])) {
				$arrayCriteria = array();
				foreach ($checkData['check']['success_criteria'] as $key => $success_criteria) {
					$success_criteria['value'] = ($success_criteria['action'] == 'http_response_time') ? (int)$success_criteria['value'] : $success_criteria['value'];
					$arrayCriteria[] = array($success_criteria['action'] => $success_criteria['value']);
				}
				unset($checkData['check']['success_criteria']);
				$checkData['check']['success_criteria'] = $arrayCriteria;
			}

			if(isset($checkData['check']['form_params'])) {
				$checkData['check']['form_params'] = array_filter($checkData['check']['form_params']);
				if(empty($checkData['check']['form_params']))
					unset($checkData['check']['form_params']);
			}

			if(isset($checkData['check']['auth'])) {
				$checkData['check']['auth'] = array_filter($checkData['check']['auth']);
				if(empty($checkData['check']['auth']))
					unset($checkData['check']['auth']);
			}
			
			$checks = array($checkData);
			saveChecks();
			$check = $checkData;

			if(!isset($check['check']['form_params'])) {
				$check['check']['form_params'] = array('' => '');
			}

			$response = array('success' => 'Check saved');
		}else{
			$response = array('error' => 'No checkid');
		}

	break;
	
	default:
		$response = array('error' => 'No action');
	break;
}

function calcPercGraph($a, $b, $limit=40, $k=5) {
	$percent = (int)(($a / $b) * 100);
	if($percent >= $limit && $k != 0) {
		$percent -= $k;
	}

	return $percent;
}

?>