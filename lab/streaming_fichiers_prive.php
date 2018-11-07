
<?php
	session_start();
	include '../includes/global_include.php';
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
			width: 320px;
			padding: 15px;
			padding-top: 30px;
            margin: auto;
            margin-top: 200px;
			box-sizing: border-box;
			text-align: center;
		}
		#menuPrincipale .title {
			font-size: 20px;
			padding-bottom: 15px;
		}
		a .btnVst {
			width: 100%;
			text-align: left;
			box-sizing: border-box;
		}
		
	</style>
</head>
<body>

<?php include '../includes/avatar.php'; ?>


<div id="menuPrincipale" class="vst2">

</div>
	
<video src="../api/stream_file.php" controls="" type="video/mp4"></video>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">


</script>
</body>
</html>