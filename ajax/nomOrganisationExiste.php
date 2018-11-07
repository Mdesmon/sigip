<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Nom organisation existe) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	if(!Controls::control_user([SUPERADMIN, ADMINISTRATEUR])) {
		$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Enregistrer session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	$parent = isset($_POST['parent']) ? $_POST['parent'] : NULL;

	if(OrganizationsManager::nameExists($_POST['nom'], $parent))
		echo "true";
	else
		echo "false";
?>