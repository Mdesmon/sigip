<?php
	session_start();
	include '../includes/global_include.php';
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier utilisateur) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}
	
	$sender = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$targetUser = UsersManager::getUser($_POST['id_user']);
	$changes = json_decode($_POST['changes']);

	if($targetUser === FALSE) {
		echo "WRONG USER ID";
		return FALSE;
	}
	
	if(!Controls::user_modify($targetUser)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier utilisateur) par ". $sender->username() .".", INCIDENT);
		return FALSE;
	}

	if(property_exists($changes, 'organization')) {
		if($changes->organization === "") {
			if($sender->organization() !== NULL) {
				echo "ACTION NON AUTORISE";
				LogsManager::addLog("Action non autorisée (Modifier organisation) par ". $sender->username() .".", INCIDENT);
				return FALSE;
			}
		}
		else {
			$org = OrganizationsManager::getOrganization($changes->organization);

			if($org === FALSE) {
				echo "WRONG ORGANIZATION ID";
				return FALSE;
			}

			if(!Controls::organization_modify($org, $sender)) {
				echo "ACTION NON AUTORISE";
				LogsManager::addLog("Action non autorisée (Modifier organisation) par ". $sender->username() .".", INCIDENT);
				return FALSE;
			}
		}
	}
	
	
	// MODIFIER USER
	UsersManager::modify($targetUser, $changes);

?>