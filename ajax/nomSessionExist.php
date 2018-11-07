<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');

	if($_POST['id_organisation'] === "TOUT") {
		echo "NO ACTIVE ORGANIZATION";
		return FALSE;
	}

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Nom Session existe) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$organization = OrganizationsManager::getOrganization($_POST['id_organisation']);

	if($organization === FALSE) {
		echo "WRONG ORGANIZATION ID";
		return FALSE;
	}
	
	if(!Controls::organization_access($organization, $user) OR $user->typeUser() == APPRENANT) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Nom Session existe) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// NAME EXISTS
	$response = SessionsManager::nameExists($_POST['nom'], $_POST['id_organisation']);

	if($response === TRUE)
		echo "true";
	else if($response === FALSE)
		echo "false";
	else
		echo $response;
?>