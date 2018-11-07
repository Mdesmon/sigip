<?php
    session_start();   
    include '../includes/global_include.php';
    header('Content-Type:text/plain');

    // SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Renommer organisation) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$organization = OrganizationsManager::getOrganization($_POST['id_organization']);

	if($organization === "WRONG ID") {
		echo "WRONG ID";
		return FALSE;
	}

	if(!Controls::organization_modify($organization, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Renommer organisation) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

    // RENAME ORGANISATION
    $response = NULL;

    if(OrganizationsManager::nameExists($_POST['newName']))
        $response = "NAME ALREADY EXISTS";

    OrganizationsManager::rename($organization, $_POST['newName']);
    
    echo $response;
?>