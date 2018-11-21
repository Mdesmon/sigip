<?php
	session_start();
	include '../includes/global_include.php';
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier établissement) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}
	
	$user = Controls::getConnectedUser();

	if(!Controls::control_user([SUPERADMIN, ADMINISTRATEUR])) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier établissement) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	$etablissement = EtablissementsManager::getEtablissement($_POST['id_etablissement']);

	if($etablissement === FALSE) {
		echo "WRONG ETABLISSEMENT ID";
		return FALSE;
	}
	
	// MODIFIER ETABLISSEMENT
	$changes = json_decode($_POST['changes']);
	EtablissementsManager::modify($etablissement, $changes);
