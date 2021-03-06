<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Enregistrer session) par un utilisateur non connecté.", INCIDENT);
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
		LogsManager::addLog("Action non autorisée (Enregistrer session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// ENREGISTRER SESSION
	echo SessionsManager::save($session, $_POST['xml'], isset($_POST['autosave']));
?>