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
	<link rel="stylesheet" type="text/css" href="../css/acceuil.css">
    
    <title>Menu principale</title>
</head>
<body>

<?php include '../includes/avatar.php'; ?>

<div id="page">
	<div class="app-logo center"></div>

	<div id="list-item">
		<a href="acceuil_sessions.php">
			<div class="item icon apps">
				<div class="title">Acceuil Sessions</div> 
			</div>
		</a>
	</div>
	
</div>
<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">


</script>
</body>
</html>