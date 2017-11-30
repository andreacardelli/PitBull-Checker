<?php

switch ($action) {	
	default:
		// file_put_contents(DIR_ROOT."/logs/404.log", json_encode($_SERVER['REQUEST_URI'])."\n", FILE_APPEND);
		header("HTTP/1.0 404 Not Found");
		die();
	break;
}

?>