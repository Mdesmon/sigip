<?php
	session_start();
	include '../includes/global_include.php';

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Liste inscrit) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$session = SessionsManager::getSession($_POST['id_session']);
	
	if($session === "WRONG ID") {
		echo "WRONG ID";
		return FALSE;
	}

	if(!Controls::session_trainerAccess($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Enregistrer session) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// LISTE INSCRIT XML
	echo InscriptionsManager::listeInscritsXML($session->name());
?>