<?php

switch ($action) {
	case 'login':
		$response = array('error' => 'Need a password and an username to login');
		if(isset($_POST['password']) && isset($_POST['username']) && !empty($_POST['username']) && !empty($_POST['password'])) {
			$user = getUser($_POST['username']);

			if($user['password'] == $_POST['password']) {
				unset($user['password']);
				$_SESSION = array(
						'user' => $user['user'],
						'email' => $user['email'],
						'role' => (isset($user['role'])) ? $user['role'] : 'user'
					);

				header('location: ' . LINK_BASE . '/');
				die();
			}else{
				$response = array('error' => 'Password incorrect');
			}
		}

	break;

	case 'logout':
		session_destroy();
		unset($_COOKIE);
		header('location: ' . $router->generate('login_index'));
		die();
	break;

	case 'index':
	default:
		
	break;
}

?>