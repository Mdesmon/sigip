<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Créer session) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$organization = OrganizationsManager::getOrganization($_POST['id_organisation']);

	if($user === FALSE) {
		echo "WRONG USER ID";
		return FALSE;
	}
	else if($organization === FALSE) {
		echo "WRONG ORGANIZATION ID";
		return FALSE;
	}
	
	if(!Controls::organization_access($organization, $user) OR $user->typeUser() == APPRENANT) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Créer session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}
	
	// CREER SESSION
	echo SessionsManager::create(
		$_POST['nom_session'],
		$_POST['id_organisation'],
		$_SESSION[APP_NAME]['idUser'],
		date("Y-m-d"), date("Y-m-d"),
		$_POST['statut'],
		$formateur_connecte = $_SESSION[APP_NAME]['username']
	);
?>