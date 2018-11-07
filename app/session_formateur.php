<?php
	session_start();

	include '../includes/global_include.php';
	
	/* SECURITE */
	if(Controls::control_user([APPRENANT])) {
		header('Location: ../index.php');
		exit();
	}
	else if(!controle_organisation()) {
		header('Location: ../index.php');
		exit();
	}
	

	$session = SessionsManager::getSession($_POST['session_a_charger']);
	$organization = OrganizationsManager::getOrganization($session->organization());

?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/theme-defaut.css">
	<link rel="stylesheet" type="text/css" href="../css/session.css">
	<link rel="stylesheet" type="text/css" href="../css/backgrounds.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	
	<title>Session</title>
</head>
<body>
	
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