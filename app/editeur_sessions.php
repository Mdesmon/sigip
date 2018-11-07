<?php
	session_start();
	
	include '../includes/global_include.php';
	
	/* SECURITE */
	if(Controls::control_user([APPRENANT])) {
		header('Location: ../index.php');
		exit();
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);
	$newSession = empty($_POST['id_session']);
	$session = NULL;
	$organization = NULL;

	if($newSession) {
		$organization = OrganizationsManager::getOrganization($_SESSION[APP_NAME]['activeOrganization']);
	}
	else {
		$session = SessionsManager::getSession($_POST['id_session']);
		$organization = OrganizationsManager::getOrganization($session->organization());

		if(Controls::session_access($session, $user)) {
			header('Location: ../index.php');
			exit();
		}
	}
	
	
	// Prepare les variables PHP
	if(!$newSession)	// Charge une sauvegarde
		$session = SessionsManager::getSession($_POST['id_session']);
	else {	// Nouvelle session
		$session = new Session(array());
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/session.css">
	<link rel="stylesheet" type="text/css" href="../css/editeur_sessions.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	
	<title>Editeur Session</title>
</head>
<body>

	<?php include '../includes/avatar.php'; ?>
	
	<div id="ecranDeChargement">
		<div class="space200"></div>
		Chargement en cours...
	</div>

<script src="../node_modules/atomjs/atom.min.js"></script>
<script src="../js/session_commun.js"></script>
<script>
	var ecranDeChargement = document.getElementById('ecranDeChargement');

</script>

</body>
</html>