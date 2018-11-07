<?php
    session_start();
    include '../includes/global_include.php';
    header('Content-Type:text/plain');

    // SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer organisation) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$organization = OrganizationsManager::getOrganization($_POST['id_organization']);

	if($organization === "WRONG ID") {
		echo "WRONG ID";
		return FALSE;
	}

	if(!Controls::organization_remove($organization, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Supprimer organisation) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

    // REMOVE ORGANIZATION
    $organization = OrganizationsManager::getOrganizationsHierarchy($_POST['id_organization']);
    $rapport = NULL;

    if($organization === FALSE)
        $rapport = "WRONG ID";
    else
        $rapport = json_encode( OrganizationsManager::remove($organization) );
    
    echo $rapport;
?>