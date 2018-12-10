<?php
	session_start();
	include '../includes/global_include.php';
	
	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Modifier Textannu) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}
	
	$sender = Controls::getConnectedUser();
	$targetStaff = StaffManager::getStaff($_POST['id']);
	$changes = json_decode($_POST['changes']);

	if($targetStaff === FALSE) {
		echo "WRONG STAFF ID";
		return FALSE;
	}
	
	
	// MODIFIER USER
	StaffManager::modify($targetStaff, $changes);
?>