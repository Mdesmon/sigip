<?php
	session_start();
	include '../includes/global_include.php';

	/* SECURITE */
	if (!Controls::isConnected() OR Controls::control_user([APPRENANT]))
	{
		header('Location: acceuil_user.php');
		exit();
	}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<?php
		include '../includes/head.html';
	?>

		<title>Importer Textannu</title>

		<link href="../css/textannu.css" rel="stylesheet">

	</head>
	<body>
		<?php include '../includes/avatar.php'; ?>
	
		<h1>Importer Textannu</h1>

		<div class="space100"></div>


		<div class="menu glass">
			<form id="form" method="post" action="../api/traitement_textannu.php" enctype="multipart/form-data">
			Fichier : <input name="file" type="file" />
			<div class="space20"></div>
			<div class="btnBlanc" id="btnSubmit" onclick="form.submit()">Envoyer</div>
			</form>
		</div>

		
		<a class="btnOutline" href="../assets/textannu.xlsx">
			<div class="icon div downloadFile"></div>
			Télécharger le modèle
		</a>
		
		



	<script src="../node_modules/atomjs/atom.js"></script>
	</body>
</html>