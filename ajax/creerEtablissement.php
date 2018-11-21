<?php
	session_start();
	include '../includes/global_include.php';

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Creer établissement) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}
	
	$user = Controls::getConnectedUser();

	if(!Controls::control_user([SUPERADMIN, ADMINISTRATEUR])) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Creer établissement) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// CREER Etablissement
	$newEtablissement = new Etablissement(array(
		'name' => $_POST['nom'],
        'rne' => $_POST['rne']
	));

	echo EtablissementsManager::create($newEtablissement);
