<?php
	session_start();
	include '../includes/global_include.php';
	header('Content-Type:text/plain');

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Copier session) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$session = SessionsManager::getSession($_POST['id_session_a_copier']);
	$organization = OrganizationsManager::getOrganization($_POST['id_organisation_destination']);

	if($session === FALSE) {
		echo "WRONG SESSION ID";
		return FALSE;
	}
	else if($organization === FALSE) {
		echo "WRONG ORGANIZATION ID";
		return FALSE;
	}
		
	if(!Controls::session_access($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Copier session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}
	
	// COPIER SESSION
	echo SessionsManager::copy(
		$session,
		$_POST['nouveau_nom_session'],
		$organization,
		($_POST['copierInscriptions'] === "true"),
		OPEN,
		$formateur_connecte = $_SESSION[APP_NAME]['username']
	);
?>