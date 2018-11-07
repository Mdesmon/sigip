<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer utilisateur) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$userToDelete = UsersManager::getUser($_POST['id_utilisateur']);

	if($userToDelete === "WRONG ID") {
		echo "WRONG ID";
		return FALSE;
	}

	if(!Controls::user_modify($userToDelete)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer utilisateur) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}
	
	// SUPPRIMER UTILISATEUR
	echo UsersManager::remove($userToDelete);
?>