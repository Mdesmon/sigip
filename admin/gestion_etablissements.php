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
	
	$etablissements = EtablissementsManager::getAllEtablissements();
    $nb_etablissements = count($etablissements);

	
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
		#menuCreer {
			width: 400px;
			display: none;
		}
		.message {
			margin: 100px 0 30px;
			text-align: center;
		}
		.pen, .wrong2 {
			cursor: pointer;
		}
	</style>
</head>
<body>
    <?php
		include '../includes/admin_header.php';
	?>

	<div class="container">
		
		<?php
			if (count($etablissements) > 0): ?>
				<table class="springtime scrollable">
					<thead>
						<tr>
							<th>Etablissement</th>
							<th>RNE</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach ($etablissements as $e) {
								echo '
									<tr>
										<td>'. $e->name() .'</td>	
										<td>'.
											$e->rne() .'
											<div class="pull-right icon wrong2" onclick="ajax_removeEtablissement('. $e->id() .')"></div>
											<!--
												<div class="pull-right tab"></div>
												<div class="pull-right icon pen"></div>
											-->
										</td>
									</tr>
								';
							}
						?>
					</tbody>
				</table>
			<?php else: ?>
				<div class='springtime message'>Il n'y a aucun établissement enregistré.</div>
			<?php endif; ?>

		<div class="center">
			<div class="btnBlanc icon plus" onclick="showMenu(menuCreer);"> Ajouter un Etablissement</div>
		</div>

	</div> <!-- .container -->
	
	<!-- MENUS -->

	<div id="voile"></div>

	<div id="menuCreer" class="menu helly">
		<div class="title">Création d'établissement</div>
		<div class="space50"></div>
		Nom de l'établissement :
		<input id="menuCreer_name" class="springtime" type="text" />
		<div class="space"></div>
		RNE : 
		<input id="menuCreer_rne" type="text" class="springtime" lenght="8" id="input_rne">
		<div id="menuCreer_name_check" class="icon div"></div>
		<div class="space50"></div>
		<div id="menuCreer_btnValider" class="btnValider btnSpringtime green icon right-medium" onclick="menuCreer_valide();"></div>
		<div id="menuCreer_btnAnnuler" class="btnAnnuler btnSpringtime red icon wrong-medium" onclick="hideMenu(menuCreer);"></div>
	</div>

	
	

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
		if(tbody)
			tbody.style.maxHeight = (getInnerHeight() - 275) + "px";
	}

	function showMenu(menu) {
		show(voile);
		show(menu);
		centerOnScreen(menu);
	}

	function hideMenu(menu) {
		hide(menu);
		hide(voile);
	}

	function menuCreer_valide() {
		if (menuCreer_name.value === "") {
			menuCreer_name.focus();
			return false;
		}
		if (menuCreer_rne.value === "") {
			menuCreer_rne.focus();
			return false;
		}

		hide(menuCreer);
		ajax_createEtablissement();
	}

	/* AJAX */
	var xhrCreateEtablissement = new XMLHttpRequest();
	var xhrChangeEtablissement = new XMLHttpRequest();
	var xhrRemoveEtablissement = new XMLHttpRequest();
	

	xhrCreateEtablissement.onreadystatechange = function() {
		var xhr = this;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		window.location.reload();
	}
	function ajax_createEtablissement() {
		xhrCreateEtablissement.open("POST", "../ajax/creerEtablissement.php", true);
		xhrCreateEtablissement.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrCreateEtablissement.send("nom="+ encodeURIComponent(menuCreer_name.value) +"&rne="+ encodeURIComponent(menuCreer_rne.value));
	}

	xhrRemoveEtablissement.onreadystatechange = function() {
		var xhr = this;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		window.location.reload();
	}
	function ajax_removeEtablissement(id) {
		if (!confirm('Êtes vous sûr de vouloir supprimer cette Etablissement?')) {
			return false;
		}

		xhrRemoveEtablissement.open("POST", "../ajax/removeEtablissement.php", true);
		xhrRemoveEtablissement.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrRemoveEtablissement.send("id=" + id);
	}

</script>

</body>
</html>