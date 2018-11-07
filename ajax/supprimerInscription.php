<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer inscription) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$inscription = InscriptionsManager::getInscription($_POST['id_inscription']);
	
	if($inscription === FALSE) {
		echo "WRONG ID";
		return FALSE;
	}

	if($inscription['user']->organization() === NULL) {
		if(!Controls::control_user([SUPERADMIN])) {
			echo "ACTION NON AUTORISE";
			LogsManager::addLog("Action non autorisée (Supprimer inscription) par ". $user->username() .".", INCIDENT);
			return FALSE;
		}
	}
	else {
		$organization = OrganizationsManager::getOrganization($inscription['user']->organization());

		if(!Controls::organization_modify($organization, $user)) {
			echo "ACTION NON AUTORISE";
			LogsManager::addLog("Action non autorisée (Supprimer inscription) par ". $user->username() .".", INCIDENT);
			return FALSE;
		}
	}

	// SUPPRIMER INSCRIPTION
	echo InscriptionsManager::remove($_POST['id_inscription']);
?>