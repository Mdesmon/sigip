<?php
	session_start();
	
	include '../includes/global_include.php';
	
	/* SECURITE */
	if(!Controls::isConnected()) {
		header('Location: ../index.php');
		exit();
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

	if(isset($_POST['session_a_charger']))
		$session = SessionsManager::getSession($_POST['session_a_charger']);
	else if(isset($_GET['session_a_charger']))
		$session = SessionsManager::getSession($_GET['session_a_charger']);
	else {
		header('Location: ../index.php');
		exit();
	}

	if(Controls::session_access($session, $user)) {
		header('Location: ../index.php');
		exit();
	}

	$sessionXML = SessionsManager::getSave($session);

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