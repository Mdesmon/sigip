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
	
    <?php
		include '../includes/head.html';
	?>
	<link rel="stylesheet" type="text/css" href="../css/backgrounds.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
    
    <title>Generation de pdf</title>
</head>
<body class="fractal1">

<?php include '../includes/avatar.php'; ?>

<div class="space25"></div>
<h1>Generation de pdf</h1>
<div class="space200"></div>

<div class="menu1 faceB">
	<form method="post" action="generer_pdf.php" enctype="multipart/form-data">
		Fichier : <input name="file" type="file" />
		<div class="space20"></div>
		<input type="submit">
	</form>
</div>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	
</script>
</body>
</html>