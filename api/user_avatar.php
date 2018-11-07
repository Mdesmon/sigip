<?php
    session_start();
    include "../includes/global_include.php";

    // SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (télécharger avatar utilisateur) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
    }

    $user = Controls::getConnectedUser();
    $path = "../content/users/". $user->folder() ."/avatar.png";

    if (!file_exists($path)) {
        $path = "../assets/default_avatar.png";
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));

    readfile($path);

?>