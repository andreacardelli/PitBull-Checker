<?php

switch ($action) {
	default:
		$stats = array(
				'success' => 0,
				'error' => 0,
				'disabled' => 0,
				'check_success' => array(),
				'check_error' => array()
			);

		$checks = getUserChecks($_SESSION['user']);
		$user = getUser($_SESSION['user']);

		foreach ($checks as $check) {
			if($check['status'] == 'disabled') {
				$stats['disabled']++;
			}elseif(isset($check['last_check'])) {
				if($check['last_check']['status'] == 'SUCCESS' || $check['last_check']['status'] == 'UP_AGAIN'){
					$stats['check_success'][] = $check;
					$stats['success']++;
				}else{
					$stats['check_error'][] = $check;
					$stats['error']++;
				}
			}
		}

		break;
}

?>