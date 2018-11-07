<?php
	session_start();
	include '../vendor/autoload.php';
	include '../includes/global_include.php';
	include '../includes/envoi_email.php';

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Changer mot de passe) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$sender = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$targetUser = UsersManager::getUser($_POST['id_user']);

	if(!Controls::user_modify($targetUser)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Changer mot de passe) par ". $sender->username() .".", INCIDENT);
		return FALSE;
	}

	// CHANGE PASSWORD
	UsersManager::changePassword($targetUser, $_POST['newPassword'], $_POST['email']);

?>