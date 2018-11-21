<?php
	session_start();
	include '../includes/global_include.php';
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer établissement) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = Controls::getConnectedUser();
	
	if(!Controls::control_user([SUPERADMIN, ADMINISTRATEUR])) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer établissement) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	$etablissement = EtablissementsManager::getEtablissement($_POST['id']);

	if($etablissement === FALSE) {
		echo "WRONG ETABLISSEMENT ID";
		return FALSE;
	}

	// SUPPRIMER INSCRIPTION
	echo EtablissementsManager::remove($etablissement);
