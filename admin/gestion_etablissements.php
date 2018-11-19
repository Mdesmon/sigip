<?php
	session_start();
	
	include '../includes/global_include.php';

	/* SECURITE */
	if (
		!Controls::isConnected()
		OR Controls::control_user([APPRENANT])
		OR (
			!Controls::inPrimaryOrganization()
			AND !Controls::organization_access(Controls::getActiveOrganization(), Controls::getConnectedUser())
		)
	)
	{
		header('Location: ../index.php');
		exit();
	}
    
    $nb_etablissements = 0;

	
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
    <link rel="stylesheet" type="text/css" href="../css/animation.css">
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<title>Gestion Etablissements</title>

	<style>
		table {
			margin: 30px 0;
		}
	</style>
</head>
<body>
    <?php
		include '../includes/admin_header.php';
	?>

	<div class="container">
		
		<table class="springtime scrollable">
			<thead>
				<tr>
					<th>Etablissement</th>
					<th>RNE</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Rectorat</td>
					<td>
						12345678
						<div class="pull-right icon wrong2"></div>
						<div class="pull-right tab"></div>
						<div class="pull-right icon pen"></div>
					</td>
				</tr>
				
			</tbody>
		</table>

		<div class="center">
			<div class="btnBlanc icon plus"> Ajouter un Etablissement</div>
		</div>
		

	</div>
	
	<div id="voile"></div>
	

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	var tbody = document.getElementsByTagName('tbody')[0];

	resize();

	
	onresize = function() {
		resize();
	};

	voile.onmousedown = function(e) {
		e.preventDefault();
	}

	function resize() {
		tbody.style.maxHeight = (getInnerHeight() - 275) + "px";
	}
</script>

</body>
</html>