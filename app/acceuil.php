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
<html>
<head>
	<?php
		include '../includes/head.html';
	?>
	<link rel="stylesheet" type="text/css" href="../css/backgrounds.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	<link rel="stylesheet" type="text/css" href="../css/acceuil.css">
    
    <title>Menu principale</title>
</head>
<body>

<?php include '../includes/avatar.php'; ?>

<div id="menuLateral" class="vst2">
	<div>LOGIN :</div>
	<a href="../login.php">
		<div class="btnVst"><div class="icon div-indent connexion"></div>Page de connexion</div>
	</a>
	<div class="space"></div>
	<div>ADMIN :</div>
	<a href="../admin/gestion_etablissements.php">
		<div class="btnVst"><div class="icon div-indent home"></div>Etablissements</div>
	</a>
	<div class="space"></div>
	<a href="../admin/parametres_generaux.php">
		<div class="btnVst"><div class="icon div-indent wheel2"></div>Parametres Generaux</div>
	</a>
	<div class="space"></div>
	<a href="../admin/creation_utilisateurs.php">
		<div class="btnVst"><div class="icon div-indent addUser"></div>Page creation utilisateurs</div>
	</a>
	<div class="space"></div>
	<a href="../admin/gestion_utilisateurs.php">
		<div class="btnVst"><div class="icon div-indent users"></div>Page gestion utilisateurs</div>
	</a>
	<div class="space"></div>
	<a href="../admin/logs.php">
		<div class="btnVst"><div class="icon div-indent spreadsheet2"></div>Logs</div>
	</a>
	
</div>

<div id="page">
	<div class="app-logo center"></div>

	<div id="list-item">
		<a href="saisie_textannu.php">
			<div class="item icon calendar-modify">
				<div class="title">Formulaire d'inscription</div> 
			</div>
		</a>

		<a href="importer_textannu.php">
			<div class="item icon cloud-upload">
				<div class="title">Importer un fichier</div> 
			</div>
		</a>

		<a href="consulter_textannu.php">
			<div class="item icon database">
				<div class="title">Consulter la base</div> 
			</div>
		</a>
		
	</div>
	
</div>
<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">


</script>
</body>
</html>