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
	
	$bdd = DB::getInstance();

	// Gestion des organisations
	if(isset($_POST['change_activeOrganization'])) {
		$activeOrganization = intval($_POST['change_activeOrganization']);
		Controls::changeActiveOrganization($_POST['change_activeOrganization']);
	}
	else {
		$activeOrganization = $_SESSION[APP_NAME]['activeOrganization'];
	}

	$allOrganizations = ($activeOrganization == -1);
	
	$logs = LogsManager::getLogsByOrganization($activeOrganization);
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	
	<title>Administration utilisateurs</title>
</head>
<body>
	<?php
		include '../includes/admin_header.php';
	?>
	
	<?php
		if(OrganizationsManager::haveSubOrganization($_SESSION[APP_NAME]['idOrganization'])) {
			echo '
				<ul id="menu-haut">
					<li>
						<form id="filtreOrganisation" action="logs.php" method="post">
							Organisations :
							<select id="selectOrganisation" name="change_activeOrganization">';
				
			options_organization(true);

			echo '			</select>
						</form>
					</li>
				</ul>';
		}
		else {
			echo '<div class="space50"></div>';
		}
	?>
	

	<table class="springtime scrollable">
		<thead>
			<tr>
				<th>Utilisateur</th>
				<th>Organisation</th>
				<th>Type</th>
				<th>Date/Heure</th>
				<th>Commentaire</th>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
				foreach ($logs as $log) {
					echo '
						<tr>
							<td>'. $log['nom_utilisateur'] .'</td>
							<td>'. $log['nom_organisation'] .'</td>
							<td>'. $log['type'] .'</td>
							<td>'. $log['datetime'] .'</td>
							<td>'. $log['commentaire'] .'</td>
						</tr>
					';
				}
			?>
		</tbody>
	</table>
	
	<a href="../app/acceuil.php">
		<div class="flecheRetour"></div>
	</a>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	var tbody = document.getElementById('tbody');
	var filtreOrganisation = document.getElementById('filtreOrganisation');
	var selectOrganisation = document.getElementById('selectOrganisation');
	
	resize();
	
	onresize = function() {
		resize();
	}
	
	if(selectOrganisation != null) {
		selectOrganisation.onchange = function() {
			filtreOrganisation.submit();
		};
	}

	function resize() {
		tbody.style.maxHeight = (getInnerHeight() - 275) + "px";
	}
</script>

</body>
</html>