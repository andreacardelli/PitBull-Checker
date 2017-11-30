<?php

switch ($action) {
	case 'settings':
		if(!empty($parameters['userid'])) {
			$user = getUser($parameters['userid']);
			if(empty($user)){
				$response = array('error' => 'Something went wrong. Userid '.$parameters['userid'].' does not exists.');
			}
		}else{
			$response = array('error' => 'No userid');
		}
	break;

	case 'save':

		if(!empty($parameters['userid'])) {
			// Retrieve old data user
			$userSaved = getUser($parameters['userid']);
			// Filter empty key
			$user = array_filter($_POST);
			
			// Available channels
			$channels = array('telegram', 'emails', 'sms');

			// Set "sent" field on update
			foreach ($channels as $channel) {
				if(isset($userSaved[$channel]['sent']) && isset($user[$channel])) {
					$user[$channel]['sent'] = $userSaved[$channel]['sent'];
				}
			}

			if(!empty($user['telegram']['chatids'])){
				$user['telegram']['chatids'] = explode(',', $user['telegram']['chatids']);
			}

			if(!empty($user['emails']['recipients'])){
				$emailArray = array();
				$user['emails']['recipients'] = explode(',', $user['emails']['recipients']);
				foreach ($user['emails']['recipients'] as $email) {
					$emailArray[] = array(
							"email" =>  $email,
			                "verified" => "no",
			                "verificaiton_code" => "sdfsfr43"
						);
				}

				$user['emails']['recipients'] = $emailArray;
			}

			if(!empty($user['sms']['numbers'])){
				$user['sms']['numbers'] = explode(',', $user['sms']['numbers']);
			}

			// Reset session user
			if($_SESSION['role'] != 'superadmin') {
				$_SESSION = array(
						'user' => $user['user'],
						'email' => $user['email']
					);
			}
			// Save user in json file
			// var_dump($user);
			saveUser($user);
		}else{
			$response = array('error' => 'No userid');
		}

	break;

	case "create":
		if(!isset($_SESSION['role']) || $_SESSION['role'] != 'superadmin') {
			header('location: ' . $router->generate('dashboard'));
		}
	break;

	case "list":

		$users = getAllUsers();

	break;
	
	default:
		$response = array('error' => 'no action');
	break;
}

?>