<?php
	session_start();
	include '../includes/global_include.php';
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Changer statut session) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$session = SessionsManager::getSession($_POST['id_session']);

	if($session === "WRONG ID") {
		echo "WRONG ID";
		return FALSE;
	}

	if(!Controls::session_modify($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Changer statut session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	echo SessionsManager::changeState($session, $_POST['statut']);
?>