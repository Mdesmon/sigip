<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Sauvegarde etat) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$session = SessionsManager::getSession($_POST['id_session']);

	if($session === FALSE) {
		echo "WRONG SESSION ID";
		return FALSE;
	}

	if(!Controls::session_trainerAccess($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Sauvegarde etat) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// SAUVEGARDE ETAT
	echo SessionsManager::saveState($session, $_POST['xml'], isset($_POST['autosave']));
?>