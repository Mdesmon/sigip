<?php
	include '../includes/global_include.php';
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/credits.css">
	
	<title>Crédits</title>
</head>
<body style="padding: 40px;">
	<div id="dots"></div>
	<div id="rescale">
		<div id="logo-autablo"></div>
		
		<div class="space25"></div>
		
		<h1>Auteur de l'application</h1>
		
		<div class="credit">GIP FCIP Lille</div>
		<div class="credit_metier"></div>
		
		<div class="space25"></div>
		
		<?php
			session_start();
			
			if( !Controls::isConnected() ) {
				echo '<a href="acceuil.php"><div class="btnModerne icon connexion"> Retour au login</div></a>';
			}
			else if( !Controls::control_user([APPRENANT]) ) {
				echo '<a href="acceuil.php"><div class="btnModerne icon apps-sharp"> Menu principal</div></a>';
			}
			else {
				echo '<a href="acceuil_sessions.php"><div class="btnModerne icon apps-sharp"> Menu principal</div></a>';
			}
		?>
	</div>
	<script>
		var divRescale = document.getElementById("rescale");

		onload = function() {
			resize();
		};

		onresize = function() {
			resize();
		};

		function resize() {
			screenHeight = window.innerHeight || document.body.clientHeight || document.documentElement.clientHeight; // innerHeight MultiSupport.

			if(screenHeight > 849)
				divRescale.style.transform = "scale(1)";
			else
				divRescale.style.transform = "scale("+ screenHeight/950 +")";
		}

		// Bloque les touches de déplacement
		onkeydown = function(e) {
			if(e.keyCode == 40) {	// Fleche bas
				return false;
			}
		};
	</script>
</body>
</html>