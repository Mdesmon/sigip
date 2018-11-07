<?php
	session_start();
	
	include 'includes/constantes.php';
	include 'includes/config.php';
	include 'class/DB.php';
	include 'class/Controls.php';
	include 'class/User.php';
	include 'class/UsersManager.php';

	
	$userExists = NULL;
	$passwordCorrect = NULL;
	$changePassword = FALSE;
	
	
	if(isset($_POST['username']) AND isset($_POST['password'])) {
		$checkPassword = Controls::password_verify($_POST['username'], $_POST['password']);

		if($checkPassword === "OK") {
			$user = UsersManager::getUserByUsername($_POST['username']);
			Controls::connect($user);
			header('Location: app/acceuil.php');
			exit();
		}
		else if($checkPassword === "WRONG USERNAME") {
			$userExists = false;
		}
		else if($checkPassword === "WRONG PASSWORD") {
			$passwordCorrect = false;
		}
	}
	else if( isset($_GET['user']) AND isset($_GET['code']) ) {
		$user = UsersManager::getUserByUsername($_GET['user']);

		if($user->folder() === $_GET['code']) {
			$changePassword = TRUE;
		}
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="shortcut icon" href="favicon.ico">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/design.css">
	<link rel="stylesheet" type="text/css" href="css/backgrounds.css">
	<link rel="stylesheet" type="text/css" href="css/animation.css">
	<link rel="stylesheet" type="text/css" href="css/overlay.css">
	<link rel="stylesheet" type="text/css" href="css/gestion.css">
	<link rel="stylesheet" type="text/css" href="node_modules/font_webatlas/dist/css/webAtlas.css">
	<link rel="stylesheet" type="text/css" href="node_modules/font_webatlas/dist/css/webAtlas-fontface.css">

	<style>
		h1 {
			margin: 25px auto 50px;
		}
		body {
			padding: 8px 0 15px;
			overflow-y: auto;
		}
		#btnAcceuil {
			padding: 0 12px;
			position: absolute;
			right: -1px;
			bottom: -50px;
		}
		.icon {
			margin-right: 7px;
			display: inline-block;
		}
		.bulleDialogue {
			width: 320px;
			margin: auto;
			display: block;
			margin-bottom: 15px;
			box-sizing: border-box;
		}
		@media (max-width: 300px) {
			h1 {
				margin: 15px auto 25px;
				font-size: 25px;
			}
			.menuLogin, .menuMdpPerdu, .menuChangerMdp {
				padding: 15px;
			}
		}
	</style>

	<title>Login</title>
</head>
<body>
	<h1>Login</h1>
	
	<div id="container_mdpPerdu" class="cache">	<!-- L'affichage sous Firefox bug si ce container est placé après container_login -->
		<div id="bulleDialogue_recuperation" class="bulleDialogue red icon circle-warning"></div>
		<div class="menuMdpPerdu glass">
			<h2>Réinitialisation du mot de passe :</h2>
			<div class="space5"></div>
			<hr />
			<div class="space20"></div>

			<div class="instruction">Indiquez votre nom d'utilisateur ci-dessous, un mail de réinitialisation sera envoyé à l'adresse mail indiqué sur ce compte.</div>
			
			<div class="space25"></div>

			<label for="username_recuperation">Nom d'utilisateur : </label>
			<input id="username_recuperation" name="username_recuperation" type="text" class="outline" />

			<div class="space50"></div>
			<div id="btnSubmit_mdpPerdu" class="btnSubmit btnBlanc">Envoyer mail de réinitialisation</div>

			<div id="btnRetourLogin" class="btnRetour btnGlass icon arrow-left"></div>
		</div>
	</div>

	<div id="container_login" class="<?php if($changePassword) echo 'cache';?>">
		<?php
			if($userExists === FALSE) {
				echo '<div class="bulleDialogue red icon circle-warning">L\'utilisateur '. $_POST['username'] .' n\'existe pas.</div>';
			} else if($passwordCorrect === FALSE) {
				echo '<div class="bulleDialogue red icon circle-warning">Le mot de passe est incorrect</div>';
			}
		?>
		
		<div class="menuLogin glass">
			<form id="form_login" action="login.php" method="post">
				<h2>Identifiez-vous :</h2>
				<div class="space5"></div>
				<hr />
				<div class="space20"></div>
				
				<label for="username">Nom d'utilisateur : </label>
				<input id="username" name="username" type="text" class="outline" />
				<div class="space20"></div>
				<label for="mdp">Mot de passe :</label>
				<div id="mdpPerdu">Mot de passe perdu ?</div>
				<input id="mdp" name="password" type="password" class="outline" />

				<div class="space50"></div>
				<div id="btnSubmit_login" class="btnSubmit btnBlanc">Se connecter</div>
			</form>
			
			<?php
				if (Controls::isConnected()) {
					if (Controls::control_user([APPRENANT]))
						$link = "app/acceuil_user.php";
					else
						$link = "app/acceuil.php";

					printf('<a href="%s"><div id="btnAcceuil" class="btnModerne"><div class="icon apps-sharp"></div>Menu principal</div></a>', $link);
				}
			?>
			
		</div>
	</div> <!-- Fin container_login -->
	
	
	<?php if($changePassword): ?>
		<div id="container_changerMdp">
			<div id="bulleDialogue_changerMdp" class="bulleDialogue red icon circle-warning"></div>

			<div class="menuChangerMdp glass">
				<h2>Entrez un nouveau mot de passe :</h2>
				<div class="space5"></div>
				<hr />
				<div class="space20"></div>
				
				<label for="newPassword">Nouveau mot de passe : </label>
				<input id="newPassword" name="newPassword" type="password" class="outline" />

				<div class="space50"></div>
				<div id="btnSubmit_changerMdp" class="btnSubmit btnBlanc">Valider le nouveau mot de passe</div>
			</div>
		</div> <!-- Fin container_login -->
	<?php endif; ?>

<script src="node_modules/atomjs/atom.js"></script>
<script src="js/overlay.js"></script>
<script type="text/javascript">
	var form_login = document.getElementById('form_login');
	var btnSubmit_login = document.getElementById('btnSubmit_login');
	var btnSubmit_mdpPerdu = document.getElementById('btnSubmit_mdpPerdu');
	var btnRetourLogin = document.getElementById('btnRetourLogin');
	var input_username = document.getElementById('username');
	var input_mdp = document.getElementById('mdp');
	var input_username_recuperation = document.getElementById('input_username_recuperation');
	var titre = document.getElementsByTagName('h1')[0];
	var mdpPerdu = document.getElementById('mdpPerdu');
	var container_login = document.getElementById('container_login');
	var container_mdpPerdu = document.getElementById('container_mdpPerdu');
	var bulleDialogue_recuperation = document.getElementById('bulleDialogue_recuperation');


	/* INITIALISATION */
	input_username.focus();


	onkeydown = function(e) {
		var k = getKey(e);

		if(k === "Enter") {
			var active = document.activeElement.id;

			if(active === "username") {
				input_mdp.focus();
			}
			else if(active === "mdp") {
				validerFormulaire();
			}
		}

		if(!focusedOnInput())
			if(isArrowPressed(e))
				e.preventDefault();		// Evite le défilement de l'écran
	};

	btnSubmit_login.onclick = function() {
		validerFormulaire();
	};

	mdpPerdu.onclick = function() {
		addClass("cache", container_login);
		removeClass("cache", container_mdpPerdu);

		titre.innerHTML = "Mot de passe perdu";
	};

	btnRetourLogin.onclick = function() {
		addClass("cache", container_mdpPerdu);
		removeClass("cache", container_login);

		titre.innerHTML = "Login";
	};

	btnSubmit_mdpPerdu.onclick = function() {
		ajax_envoiMailRecuperation();
	};

	function validerFormulaire() {
		if(input_username.value.trim() === "") {
			afficherMessage("BANDEAU", "Veuillez entrer un nom d'utilisateur.", 'warning');
			input_username.focus();
		}
		else if(input_mdp.value.trim() === "") {
			afficherMessage("BANDEAU", "Veuillez entrer un mot de passe.", 'warning');
			input_mdp.focus();
		}
		else
			form_login.submit();
	}

	function afficherMessageErreurRecuperation(msg) {
		bulleDialogue_recuperation.innerHTML = msg;
		showBlock(bulleDialogue_recuperation);
	}

	/* AJAX */

	xhrMailRecuperation = new XMLHttpRequest();

	function ajax_envoiMailRecuperation() {
		xhrMailRecuperation.open("POST", "ajax/envoiMailRecuperation.php", true);
		xhrMailRecuperation.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrMailRecuperation.send("username="+ encodeURIComponent(username_recuperation.value));
	}

	xhrMailRecuperation.onreadystatechange = function() {
		var xhr = xhrMailRecuperation;

		if (xhr.readyState === xhr.DONE && xhr.status === 200) {	// DONE = 4 ; 200 = OK
			if(xhr.response === "OK") {
				afficherMessage('BANDEAU', 'Un mail vous à été envoyé', 'circle-info');
				hide(bulleDialogue_recuperation);
			}
			else
				afficherMessageErreurRecuperation(xhr.response);
		}
	}
	
	
	<?php if($changePassword): ?>
		titre.innerHTML = "Changement de mot de passe";

		var bulleDialogue_changerMdp = document.getElementById('bulleDialogue_changerMdp');
		var xhrChangerMdp = new XMLHttpRequest();


		btnSubmit_changerMdp.onclick = function() {
			ajax_changerMdp();
		};


		function afficherMessageErreurChangerMdp(msg) {
			bulleDialogue_changerMdp.innerHTML = msg;
			showBlock(bulleDialogue_changerMdp);
		}

		function ajax_changerMdp() {
			xhrChangerMdp.open("POST", "ajax/changerMdp_recuperation.php", true);
			xhrChangerMdp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
			<?php
				echo 'xhrChangerMdp.send( "newPassword=" + encodeURIComponent(newPassword.value) + "&username="+ encodeURIComponent("'. $_GET['user'] .'") + "&code=" + encodeURIComponent("'. $_GET['code'] .'") );';
			?>
		}

		xhrChangerMdp.onreadystatechange = function() {
			var xhr = xhrChangerMdp;

			if (xhr.readyState === xhr.DONE && xhr.status === 200) {	// DONE = 4 ; 200 = OK
				if(xhr.response === "OK") {
					afficherMessage('BANDEAU', 'Votre mot de passe à bien été modifié. Vous pouvez vous connecter dès à présent.', 'circle-info');
					hide(bulleDialogue_changerMdp);
					hide(container_changerMdp);
					removeClass("cache", container_login);
					titre.innerHTML = "Login"
				}
				else if(xhr.response !== "") {
					afficherMessageErreurChangerMdp(xhr.response);
				}
			}
		}
		
	<?php endif; ?>

</script>

</body>
</html>