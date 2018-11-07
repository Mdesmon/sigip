<?php
	session_start();
	include '../includes/global_include.php';

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Changer organisation active) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

	if($_POST['organization_id'] == NO_ACTIVE_ORGANIZATION) {
		if(!Controls::control_user([SUPERADMIN])) {
			echo "ACTION NON AUTORISE";
			LogsManager::addLog("Action non autorisée (Changer organisation active) par ". $user->username() .".", INCIDENT);
			return FALSE;
		}
	}
	else {
		$organization = OrganizationsManager::getOrganization($_POST['organization_id']);

		if($organization === "WRONG ID") {
			echo "WRONG ID";
			return FALSE;
		}

		if(!Controls::organization_access($organization, $user)) {
			echo "ACTION NON AUTORISE";
			LogsManager::addLog("Action non autorisée (Changer organisation active) par ". $user->username() .".", INCIDENT);
			return FALSE;
		}
	}
	
	// CHANGER ORGANISATION ACTIVE
	Controls::changeActiveOrganization($_POST['organization_id']);

	echo "OK";
?>