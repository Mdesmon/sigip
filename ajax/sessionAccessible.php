<?php
	session_start();
	include '../includes/global_include.php';

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Session accessible) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$session = SessionsManager::getSession($_POST['id_session']);
	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

	if($session === FALSE) {
		echo "WRONG SESSION ID";
		exit();
	}
	
	if(!Controls::session_access($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Session accessible) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	if($session->state() == OPEN)
		echo "true";
	else
		echo "false";
	
?>