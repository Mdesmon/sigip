<?php
	session_start();
	include '../includes/global_include.php';
	
	header('Content-Type:text/plain');
	
	$parent = (isset($_POST['parent']) AND $_POST['parent'] != "") ? $_POST['parent'] : NULL;


	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Creer organisation) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$organization = OrganizationsManager::getOrganization($parent);
	
	if(
		($user->typeUser() != SUPERADMIN AND $parent === NULL)
		OR ($parent !== NULL AND !Controls::organization_modify($organization, $user))
	) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Creer organisation) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// CREER ORGANISATION
	$newOrganization = new Organization(array(
		'name' => $_POST['nom'],
        'parent' => $parent
	));

	echo OrganizationsManager::create($newOrganization);
?>