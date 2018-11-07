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
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	
	<title>Paramètres généraux</title>
</head>
<body>
	<?php
		include '../includes/admin_header.php';
	?>
	
	<h2>Organisations</h2>

	<div id="tableOrganisations">
		<?php
			$organisations = OrganizationsManager::getAllOrganizations();
			
			// Affiche les organisations
			foreach($organisations as $org) {
				echo '
					<div class="organisation">
						'. $org->name() .'
						<div class="btnPen" onclick="ajax_renomerUneOrganisation('. $org->id() .')"></div>
						<div class="btnTrash" onclick="ajax_supprimerUneOrganisation('. $org->id() .',\''. $org->name() .'\')"></div>
					</div>';
				
				// Affiche les sous organisations
				foreach($org->subOrganizations() as $subOrg) {
					echo '
						<div class="sousOrganisation">
							'. $subOrg->name() .'
							<div class="btnPen" onclick="ajax_renomerUneOrganisation('. $subOrg->id() .')"></div>
							<div class="btnTrash" onclick="ajax_supprimerUneOrganisation('. $subOrg->id() .',\''. $subOrg->name() .'\')"></div>
						</div>';
				}

				echo '<div class="ajouterSousOrganisation" onclick="creerUneSousOrganisation('. $org->id() .')">AJOUTER UNE SOUS ORGANISATION</div>';
			}
		?>
	</div>

	<div class="space25"></div>

	<div id="btnAjouterOrganisation" class="btnBlanc" onclick="creerUneOrganisation()">
		<div class="icon div-indent circle-plus"></div>Ajouter une Organisation
	</div>
	
	<a href="../app/acceuil.php">
		<div class="flecheRetour"></div>
	</a>

	<div class="space25"></div>
	
<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	var xhrNomOrganisationExiste = new XMLHttpRequest();
	var xhrCreerOrganisation = new XMLHttpRequest();
	var xhrNomSousOrganisationExiste = new XMLHttpRequest();
	var xhrCreerSousOrganisation = new XMLHttpRequest();
	var xhrRenomerOrganisation = new XMLHttpRequest();
	var xhrSupprimerOrganisation = new XMLHttpRequest();
	var nomNouvelleOrganisation = null;
	var nouveauNom = null;
	var parentNouvelleOrganisation = null;
	
	function creerUneOrganisation() {
		nomNouvelleOrganisation = prompt("Nom de l'organisation :");

		if(!nomNouvelleOrganisation)
			return false;
		
		ajax_nomOrganisationExiste();
	}

	function creerUneSousOrganisation(parent) {
		nomNouvelleOrganisation = prompt("Nom de la sous organisation :");

		if(!nomNouvelleOrganisation)
			return false;
		
		parentNouvelleOrganisation = parent;
		ajax_nomSousOrganisationExiste();
	}
	

	// AJAX

	xhrNomOrganisationExiste.onreadystatechange = function() {
		var xhr = xhrNomOrganisationExiste;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		if(xhr.response === "false") {
			ajax_creerUneOrganisation();
		}
		else {
			alert('Le nom que vous avez spécifié existe déjà.')
		}
	}
	
	xhrCreerOrganisation.onreadystatechange = function() {
		var xhr = xhrCreerOrganisation;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrNomSousOrganisationExiste.onreadystatechange = function() {
		var xhr = xhrNomSousOrganisationExiste;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		if(xhr.response === "false") {
			ajax_creerUneSousOrganisation();
		}
		else {
			alert('Le nom que vous avez spécifié existe déjà.')
		}
	}
	
	xhrCreerSousOrganisation.onreadystatechange = function() {
		var xhr = xhrCreerSousOrganisation;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrRenomerOrganisation.onreadystatechange = function() {
		var xhr = xhrRenomerOrganisation;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		if(xhrRenomerOrganisation.response === "NAME ALREADY EXISTS") {
			alert("Ce nom d'organisation existe déjà.");
			return false;
		}

	    window.location.reload();
	}

	xhrSupprimerOrganisation.onreadystatechange = function() {
		var xhr = xhrSupprimerOrganisation;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		var rapport = JSON.parse(xhr.response);

		alert("Les éléments suivants ont été supprimé :\n\n- " + rapport.organizations + " organisation(s)\n- " + rapport.users + " utilisateur(s)\n- " + rapport.sessions + " session(s)");

	    window.location.reload();
	}

	function ajax_nomOrganisationExiste() {
		xhrNomOrganisationExiste.open("POST", "../ajax/nomOrganisationExiste.php", true);
		xhrNomOrganisationExiste.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrNomOrganisationExiste.send("nom="+ encodeURIComponent(nomNouvelleOrganisation));
	}

	function ajax_creerUneOrganisation() {
		xhrCreerOrganisation.open("POST", "../ajax/creerOrganisation.php", true);
		xhrCreerOrganisation.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrCreerOrganisation.send("nom="+ encodeURIComponent(nomNouvelleOrganisation));
	}

	function ajax_nomSousOrganisationExiste() {
		xhrNomSousOrganisationExiste.open("POST", "../ajax/nomOrganisationExiste.php", true);
		xhrNomSousOrganisationExiste.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrNomSousOrganisationExiste.send("nom="+ encodeURIComponent(nomNouvelleOrganisation) +"&parent="+ parentNouvelleOrganisation);
	}

	function ajax_creerUneSousOrganisation() {
		xhrCreerSousOrganisation.open("POST", "../ajax/creerOrganisation.php", true);
		xhrCreerSousOrganisation.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrCreerSousOrganisation.send("nom="+ encodeURIComponent(nomNouvelleOrganisation) +"&parent="+ parentNouvelleOrganisation);
	}

	function ajax_renomerUneOrganisation(idOrganisation) {
		while(!nouveauNom)
			nouveauNom = prompt("Veuillez entrer un nom d'organisation");

		xhrRenomerOrganisation.open("POST", "../ajax/renameOrganization.php", true);
		xhrRenomerOrganisation.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrRenomerOrganisation.send("newName="+ encodeURIComponent(nouveauNom) +"&id_organization="+ idOrganisation);

		nouveauNom = "";
	}

	function ajax_supprimerUneOrganisation(idOrganisation, nomOrganisation) {
		while(!confirm("Etes vous sur de vouloir supprimer l'organisation " + nomOrganisation + " ainsi que toute les sessions et utilisateurs qui lui sont associé ? Cette action est irreversible."))
			return false;

		xhrSupprimerOrganisation.open("POST", "../ajax/removeOrganization.php", true);
		xhrSupprimerOrganisation.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrSupprimerOrganisation.send("id_organization="+ idOrganisation);
	}
</script>

</body>
</html>