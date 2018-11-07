<?php
	session_start();
	include '../includes/global_include.php';

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier session) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$session = SessionsManager::getSession($_POST['id_session']);
	
	if($session === FALSE) {
		echo "WRONG SESSION ID";
		return FALSE;
	}
	
	if(!Controls::session_modify($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// MODIFIER SESSION
	$changes = json_decode($_POST['changes']);

	if(property_exists($changes, 'name')) {
		if(!SessionsManager::nameExists($changes->name, $session->organization()))
			SessionsManager::rename($session, $changes->name);
		else {
			echo "NAME ALREADY EXISTS";
			return false;
		}
	}
	
	if(property_exists($changes, 'state'))
		SessionsManager::changeState($session, $changes->state);

?>