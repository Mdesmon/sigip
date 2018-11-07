<?php
	session_start();
	include '../includes/global_include.php';

	/* SECURITE */
	if (!Controls::isConnected() OR Controls::control_user([APPRENANT]))
	{
		header('Location: ../login.php');
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
	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
    
    <title>Le Lab</title>

	<style>
		body {
			padding: 0;
		}
		#menuPrincipale {
			width: 280px;
			height: 100%;
			padding: 15px;
			padding-top: 30px;
			margin: auto;
			box-sizing: border-box;
			text-align: center;
		}
		#menuPrincipale .title {
			font-size: 20px;
			padding-bottom: 15px;
		}
		#menuPrincipale .btnVst {
			text-align: left;
			display: block;
		}
		
	</style>
</head>
<body>

<?php include '../includes/avatar.php'; ?>

<div id="menuPrincipale" class="vst2">
	<div class="title">LE LAB</div>
	<a href="../lab/demo_css.php">
		<div class="btnVst"><div class="icon div-indent screen"></div>Demo CSS</div>
	</a>
	<div class="space"></div>
	<a href="../lab/streaming_fichiers_prive.php">
		<div class="btnVst"><div class="icon div-indent circle-play"></div>Streaming fichiers privé</div>
	</a>
	<div class="space"></div>
	<a href="../lab/formulaire_envoi_fichier.php">
		<div class="btnVst"><div class="icon div-indent attachment"></div>Formulaire envoi de fichier</div>
	</a>
	<div class="space"></div>
	<a href="../lab/formulaire_creation_pdf.php">
		<div class="btnVst"><div class="icon div-indent cloud-download"></div>Generation de PDF</div>
	</a>
	<div class="space"></div>
	<a href="../lab/nodejs.php">
		<div class="btnVst"><div class="icon div-indent hex-rounded"></div>NodeJS</div>
	</a>
	<div class="space"></div>
	<a href="../lab/threejs.php">
		<div class="btnVst"><div class="icon div-indent cube-stroke"></div>Three.js</div>
	</a>
	<div class="space"></div>
	<a href="../lab/particlesjs.php">
		<div class="btnVst"><div class="icon div-indent checkerboard"></div>Particles.js</div>
	</a>

	
</div>
	
</div>
<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">


</script>
</body>
</html>