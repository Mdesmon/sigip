<?php
    session_start();
    include "../includes/global_include.php";

    // SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (télécharger logo organisation) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
    }
    
    $path = '';

    $user = Controls::getConnectedUser();
    $organization = Controls::getUserOrganization();
    
    if ($organization === FALSE)
        $path = "../img/logo.png";
    else
        $path = "../content/organizations/". $organization->id() ."/logo.png";

    if (!file_exists($path)) {
        $path = "../img/logo.png";
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));

    readfile($path);
    exit;
