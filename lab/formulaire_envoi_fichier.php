<?php
	session_start();
	include '../includes/global_include.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="shortcut icon" href="favicon.png">
	
    <link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/design.css">
	<link rel="stylesheet" type="text/css" href="../css/backgrounds.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
    
    <title>Formulaire</title>
</head>
<body class="fractal1">

<?php include '../includes/avatar.php'; ?>

<div class="space25"></div>
<h1>Formulaire de fichier</h1>
<div class="space200"></div>

<div class="menu1 faceB">
	<form method="post" action="traitement_fichier.php" enctype="multipart/form-data">
		Fichier : <input name="file" type="file" />
		<div class="space20"></div>
		<input type="submit">
	</form>
</div>

<a href="../index.php">
	<div id="btnMenuPrincipale" class="btnGlass">Menu principale</div>
</a>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	
</script>
</body>
</html>