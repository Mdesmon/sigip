<?php
	session_start();
	include '../includes/global_include.php';
	
	if(!Controls::isConnected()) {
		echo "NOT CONNECTED";
		return FALSE;
	}

	Controls::changeViewSessionState($_POST['state']);

	echo "OK";
?>