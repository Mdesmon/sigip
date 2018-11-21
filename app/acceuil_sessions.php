<?php
	session_start();
	
	include '../includes/global_include.php';

	if( !Controls::isConnected() ) {
		header('Location: ../index.php');
		exit();
	}
	
	$user = Controls::getConnectedUser();
	$sessions = SessionsManager::getSessionsByUser($user);

?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>
	
	<link rel="stylesheet" type="text/css" href="../css/acceuil_sessions.css">
	
	<title>Bienvenue</title>

	<style>
		body {
			padding: 40px;
		}
		.session {
			cursor: pointer;
		}
	</style>
</head>
<body>

	<?php include '../includes/avatar.php'; ?>

	<form action="session_apprenant.php" method="post" style="display: none">
		<input id="inputSession" name="session_a_charger" type="number" value="-1" />
	</form>
	<div id="dots"></div>
	
	<p>Bonjour <?php echo $_SESSION[APP_NAME]['username']; ?>.</p>

	<div id="app-logo"></div>
	
	<div id="block-sessions">
		<h1>Sessions</h1>
		
		<?php
			if (count($sessions) > 0) {
				foreach ($sessions as $s) {
					if ($s->state() != OPEN)
						continue;
					
					echo "
						<div class='session steam' onclick='entrerSession(". $s->id() .")'>
							<div class='titre'>". $s->name() ."</div>
						</div>
					";
				}
			}
			else {
				echo '<div class="space100"></div>';
				echo "<div class='springtime message'>Il n'y a aucune session de disponible pour vous.</div>";
			}
			
		?>
	</div>
	
	<?php include "../includes/creditsHTML.php"; ?>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	var id_utilisateur = <?php echo $_SESSION[APP_NAME]['idUser']; ?>;
	var form = document.getElementsByTagName('form')[0];
	var inputSession = document.getElementById('inputSession');
	
	var xhr = new XMLHttpRequest();
	
	/* FUNCTIONS */
	
	function entrerSession(id_session) {
		inputSession.value = id_session;
		ajax_sessionAccessible(id_session, id_utilisateur);
	}
	
	/* AJAX */
	
	xhr.onreadystatechange = function() {
	    if (xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
	    
	    if(xhr.responseText == "true")
	    	form.submit();
	    else {
	    	alert('Vous ne pouvez pas entrer dans cette session car elle n\'est pas ouverte.');
	    	window.location.reload();
	    }
	};
	
	
	function ajax_sessionAccessible(id_session, id_utilisateur) {
		xhr.open("POST", "../ajax/sessionAccessible.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhr.send("id_session="+ encodeURIComponent(id_session) +"&id_utilisateur="+ encodeURIComponent(id_utilisateur));
	}
	
</script>

</body>
</html>