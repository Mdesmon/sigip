<?php
	session_start();
	
	include '../includes/global_include.php';

	$nb_sessions = 0;

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
	
	// Gestion des organisations
	$activeOrganization_id = $_SESSION[APP_NAME]['activeOrganization'];
	$allOrganizations = ($activeOrganization_id == NO_ACTIVE_ORGANIZATION);

	// Gestion des sessions
	if($allOrganizations)
		$sessions = SessionsManager::getAllSessions();
	else
		$sessions = SessionsManager::getSessionsByOrganization( OrganizationsManager::getOrganizationsHierarchy($activeOrganization_id) );

	// Gestion des états
	$etat = $_SESSION[APP_NAME]['viewSessionState'];

?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
	<link rel="stylesheet" type="text/css" href="../css/gestion_sessions.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	
	<title>Gestion sessions</title>
</head>
<body>

	<?php include '../includes/avatar.php'; ?>

	<form id="formCreerSession" action="../app/editeur_sessions.php" method="post">
		<input name="id_organisation" type="hidden" value="<?php echo $activeOrganization_id ?>" />
	</form>
	<form id="formModifierSession" action="../app/editeur_sessions.php" method="post">
		<input id="session_a_modifier" name="id_session" type="hidden" />
	</form>
	<form id="formEntrerSession" action="../app/session_formateur.php" method="post">
		<input id="session_de_destination" name="session_a_charger" type="hidden" />
	</form>
	<h1>Gestion sessions</h1>


	<ul id="menu-haut">
		<li>
			Etat : <select name="etat" id="select_etat">
				<option value="<?php echo OPEN; ?>"		<?php if($etat == OPEN) 	echo 'selected'; ?>>Ouvert</option>
				<option value="<?php echo CLOSED; ?>"	<?php if($etat == CLOSED) 	echo 'selected'; ?>>Fermé</option>
				<option value="<?php echo DRAFT; ?>"	<?php if($etat == DRAFT) 	echo 'selected'; ?>>Brouillon</option>
				<option value="<?php echo MODEL; ?>"	<?php if($etat == MODEL) 	echo 'selected'; ?>>Modèle</option>
				<option value="<?php echo ARCHIVED; ?>"	<?php if($etat == ARCHIVED)	echo 'selected'; ?>>Archivé</option>
				<option value="<?php echo DUSTBIN; ?>"	<?php if($etat == DUSTBIN)	echo 'selected'; ?>>Corbeille</option>
			</select>
		</li>
		<div class="div icon circle-info hoverScale" onclick="showBlock(info_etats);centerOnScreen(info_etats);"></div>
		<div class="tab"></div>
		<li>
			<?php
				if (Controls::control_user([SUPERADMIN]) OR OrganizationsManager::haveSubOrganization($_SESSION[APP_NAME]['idOrganization'])) {
					echo '
						<form id="filtreOrganisation" action="gestion_sessions.php" method="post">
						Organisations : <select id="selectOrganisation" name="change_activeOrganization">';

					options_organization(true);

					echo '
						</select>
						</form>';
				}
				else {
					echo '
						</form>
						<div class="space25"></div>';
				}

			?>
		</li>
	</ul>

	<table class="springtime scrollable">
		<thead>
			<tr>
				<th>Nom de la session</th>
				<th style="width: 170px;">Organisation</th>
				<th style="width: 180px;">Dates</th>
				<th style="width: 260px;">Actions</th>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
				foreach($sessions as $s) {
					if($s->state() != $etat)
						continue;

					echo '
						<tr>
							<td>'. $s->name() .'</td>
							<td style="width: 170px;">'. OrganizationsManager::getOrganization($s->organization())->name() .'</td>
							<td style="width: 180px;">'. formatDate($s->dateStart(), TRUE) .' au '. formatDate($s->dateEnd(), TRUE) .'</td>
							
							<td style="width: 260px;">';
					
					
					/* ACTIONS */

					$modificationAutorise = (Controls::control_user([SUPERADMIN, ADMINISTRATEUR]) OR $s->author() == $_SESSION[APP_NAME]['idUser']);
					
					if($etat == MODEL) {
						echo '		<div class="btnSpringtime green" onclick="entrerSession('. $s->id() .')">Entrer</div>';
					
						if($modificationAutorise) {
							echo '  <div class="btnSpringtime blue" onclick="copierSession('. $s->id() .')">Copier</div>
									<div class="btnSpringtime" onclick="afficherFenetreModifier('. $s->id() .')">Modifier</div>
									<div class="btnSpringtime red" onclick=\'deplacerDansCorbeille("'. $s->id() .'")\'>Supprimer</div>';	// Le premier espace est important
						}
							
						echo '
								</td>
							</tr>
						';
					}
					else if($etat == DUSTBIN) {
						if($modificationAutorise) {
							echo '  <div class="btnSpringtime green" onclick="restaurerSession('. $s->id() .')">Restaurer</div>
									<div class="btnSpringtime red" onclick=\'ajax_supprimerSession("'. $s->id() .'")\'>Supprimer définitivement</div>';	// Le premier espace est important
						}
					}
					else if($etat == ARCHIVED){
						echo '		<div class="btnSpringtime green" onclick="entrerSession('. $s->id() .')">Consulter</div>';
					
						if($modificationAutorise) {
							echo '  <div class="btnSpringtime" onclick="afficherFenetreModifier('. $s->id() .')">Modifier</div>
									<div class="btnSpringtime red" onclick=\'deplacerDansCorbeille("'. $s->id() .'")\'>Supprimer</div>';	// Le premier espace est important
						}
							
						echo '
								</td>
							</tr>
						';
					}
					else {
						echo '		<div class="btnSpringtime green" onclick="entrerSession('. $s->id() .')">Entrer</div>';
					
						if($modificationAutorise) {
							echo '  <div class="btnSpringtime" onclick="afficherFenetreModifier('. $s->id() .')">Modifier</div>
									<div class="btnSpringtime red" onclick=\'deplacerDansCorbeille("'. $s->id() .'")\'>Supprimer</div>';	// Le premier espace est important
						}
							
						echo '
									<div class="btnSpringtime blue" onclick="afficherAutresActions(event, '. $s->id() .')">Autres</div>
								</td>
							</tr>
						';
					}
					
					$nb_sessions++;
				}
			?>
		</tbody>
	</table>
	
	
	<a href="../app/acceuil.php">
		<div class="flecheRetour"></div>
	</a>
	
	<div id="nombre_entree">Nombre d'entrées : <?php echo $nb_sessions; ?></div>
	
	<a href="../app/acceuil_sessions.php">
		<div id="btn-acceuil_sessions" class="btnBlanc2"><div class="icon div-indent menu"></div>Acceuil Sessions</div>
	</a>

	<div id="btn-ajouter" class="btnBlanc2">
		<div class="icon div-indent circle-plus"></div>Ajouter une session
	</div>
	
	<div id="btn-export-csv" class="btnBlanc"><div class="icone-exportCSV"></div>Export CSV</div>
	
	<div id="voile"></div>

	<ul id="autresActions" class="glass">
		<li onclick="copierSession(selectedSession)"><div class="icon div-indent copy"></div>Copier</li>
		<li onclick="lienSession(selectedSession)"><div class="icon div-indent link"></div>Lien</li>
		<li onclick="codeSession(selectedSession)"><div class="icon div-indent qrcode"></div>Code</li>
	</ul>
	
	<div id="popupLien" class="popup glass">
		<div class="text">Copier/coller ce lien dans la barre URL pour accéder directement à la session.</div>
		<div class="space15"></div>
		<input id="inputLien" type="text" />
		<div class="space50"></div>
		<div id="btnFermerPopupLien" class="btnModerne center">Fermer</div>
		
		<div class="bumpTop"></div>
		<div class="bumpBottom"></div>
	</div>
	
	<div id="popupCode" class="popup glass">
		<div class="text">Donnez ce code aux apprenants afin qu'ils puissent s'inscrire d'eux-même et accéder à la session.</div>
		<div class="space15"></div>
		<input id="inputCode" type="text" />
		<div class="space50"></div>
		<div id="btnFermerPopupCode" class="btnModerne center">Fermer</div>
		
		<div class="bumpTop"></div>
		<div class="bumpBottom"></div>
	</div>

	<div id="fenetreModifier" class="helly">
		<div class="title">Paramètre de la sessions</div>
		<div class="space50"></div>
		Nom de la session : <input id="fenetreModifier_name" class="springtime" type="text" /> <div id="fenetreModifier_name_check" class="icon div"></div>
		<div class="space"></div>
		Etat de la session :
		<select name="etat" id="fenetreModifier_state">
			<option value="<?php echo OPEN; ?>">Ouvert</option>
			<option value="<?php echo CLOSED; ?>">Fermé</option>
			<option value="<?php echo DRAFT; ?>">Brouillon</option>
			<option value="<?php echo MODEL; ?>">Modèle</option>
			<option value="<?php echo ARCHIVED; ?>">Archivé</option>
			<option value="<?php echo DUSTBIN; ?>">Corbeille</option>
		</select>

		<div class="space25"></div>
		<div id="fenetreModifier_btnModifier" class="btnHelly blue icon pen3" onclick="modifierSession()">Modifier la session</div>
		<div class="space50"></div>
		<div id="fenetreModifier_btnValider" class="btnSpringtime green icon right-medium" onclick="fenetreModifier_valide();"></div>
		<div id="fenetreModifier_btnAnnuler" class="btnSpringtime red icon wrong-medium" onclick="hide(fenetreModifier);hide(voile);"></div>
	</div>

	<div id="info_etats" class="deepBlue" onclick="hide(this);">
		<div class="title">Etats</div>
		<div class="space25"></div>
		<div class="center">Les sessions peuvent avoir différents statuts dont les caractéristiques sont décrites ci-dessous :</div>
		<div class="space25"></div>
		<span class="bold">Ouvert :</span>
		Seules les sessions ayant le statut "Ouvert" sont accessibles par les apprenants. Si vous ne souhaitez pas qu'une sessions soit accessible, vous devez changer son statut en "Fermé".
		<div class="space"></div>
		<span class="bold">Fermé :</span>
		Ce statut permet de conserver des sessions tout en bloquant l'accès aux apprenants.
		<div class="space"></div>
		<span class="bold">Brouillon :</span>
		Ce trouve ici les sessions en cours de fabrication.
		<div class="space"></div>
		<span class="bold">Modèle :</span>
		Ces sessions servent de modèle de base pour la création d'autres sessions.
		<div class="space"></div>
		<span class="bold">Archivé :</span>
		Les sessions considérées comme terminées peuvent être gardées à des fins de consultation.
		<div class="space"></div>
		<span class="bold">Corbeille :</span>
		Les sessions supprimées sont d'abord transférées sous ce statut avant d'être réellement supprimées.
	</div>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	var tbody = document.getElementById('tbody');
	var select_etat = document.getElementById('select_etat');
	var formCreerSession = document.getElementById('formCreerSession');
	var formModifierSession = document.getElementById('formModifierSession');
	var formEntrerSession = document.getElementById('formEntrerSession');
	var session_a_modifier = document.getElementById('session_a_modifier');
	var session_de_destination = document.getElementById('session_de_destination');
	var btnAjouterSession = document.getElementById('btn-ajouter');
	var popupLien = document.getElementById('popupLien');
	var btnFermerPopupLien = document.getElementById('btnFermerPopupLien');
	var inputLien = document.getElementById('inputLien');
	var popupCode = document.getElementById('popupCode');
	var btnFermerPopupCode = document.getElementById('btnFermerPopupCode');
	var inputCode = document.getElementById('inputCode');
	var divOrganisation = document.getElementById('divOrganisation');
	var filtreOrganisation = document.getElementById('filtreOrganisation');
	var selectOrganisation = document.getElementById('selectOrganisation');
	var autresActions = document.getElementById('autresActions');
	var info_etats = document.getElementById('info_etats');
	var fenetreModifier = document.getElementById('fenetreModifier');
	var fenetreModifier_name = document.getElementById('fenetreModifier_name');
	var fenetreModifier_name_check = document.getElementById('fenetreModifier_name_check');
	var fenetreModifier_state = document.getElementById('fenetreModifier_state');
	var fenetreModifier_btnModifier = document.getElementById('fenetreModifier_btnModifier');
	var voile = document.getElementById('voile');

	var selectedSession = null;	// Pour copier, lien et code
	var nouveauNom = null;	// nom de la copie
	var elements_repositioning = [popupLien, popupCode, info_etats, fenetreModifier];
	var elements_reset = [popupLien, popupCode, info_etats, fenetreModifier];

	<?php
		if(is_null($activeOrganization_id) OR $activeOrganization_id == NO_ACTIVE_ORGANIZATION)
			echo 'var organisation_actuel = "TOUT";';
		else
			echo 'var organisation_actuel = '. $activeOrganization_id .';';
	?>
	

	<?php
		echo 'var sessions = ' . SessionsManager::serializeSessions($sessions) . ';';

		if ($_SESSION[APP_NAME]['idOrganization'] !== NULL)
			echo 'var organizations = ' . json_encode(OrganizationsManager::getOrganizationsHierarchy($_SESSION[APP_NAME]['idOrganization'])) . ';';
	 	else
	 		echo 'var organizations = ' . json_encode(OrganizationsManager::getAllOrganizations()) . ';';
	?>


	resize();
	hide(fenetreModifier);
	
	/* EVENTS */

	onresize = function() {
		resize();
	}

	function resize() {
		tbody.style.maxHeight = (getInnerHeight() - 275) + "px";
		
		// Recentre au milieu de l'écran tout les éléments qui doivents l'être
		for(var i=0; i < elements_repositioning.length ;i++) {
			var e = elements_repositioning[i];

			if(isDisplayed(e)) {
				centerOnScreen(e);
			}
		}
	}
	
	onmousedown = function(e) {
		if(whichClick(e) == "GAUCHE") {
			if(!mouseover(e, autresActions))
				autresActions.style.display = "none";
		}
		else if(whichClick(e) == "DROIT") {
			/* CODE */
		}
	};

	btnAjouterSession.onclick = function() {
		if(organisation_actuel != "TOUT")
			formCreerSession.submit();
		else
			alert('Vous devez choisir une organisation pour creer une session');
	};

	btnFermerPopupLien.onclick = function() {
		fermerPopupLien();
	}
	
	btnFermerPopupCode.onclick = function() {
		fermerPopupCode();
	}
	
	if(selectOrganisation != null) {
		selectOrganisation.onchange = function() {
			ajax_changeActiveOrganization(this.value);
		};
	}

	select_etat.onchange = function() {
		ajax_changeViewSessionState(this.value);
	};

	fenetreModifier_name.onchange = function() {
		if(fenetreModifier_name.value === sessions[selectedSession].name) {
			fenetreModifier_name_check.className = "div icon";
			fenetreModifier_name_check.innerHTML = "";
		}
		else
			ajax_nameExists(fenetreModifier_name.value, sessions[selectedSession].organization);
	};

	voile.onmousedown = function(e) {
		e.preventDefault();
	}

	/* FUNCTIONS */

	function entrerSession(index) {
		session_de_destination.value = index;
		formEntrerSession.submit();
	}
	
	function creerSession() {
		formCreerSession.submit();
	}

	function afficherFenetreModifier(session_id) {
		var session = sessions[session_id];

		selectedSession = session_id;

		hide(popupLien);
		hide(popupCode);
		
		show(voile);
		show(fenetreModifier);
		fenetreModifier_name.value = session['name'];
		fenetreModifier_state.value = session['state'];
		fenetreModifier_name_check.className = "div icon";
		fenetreModifier_name_check.innerHTML = "";
	}

	function fenetreModifier_valide() {
		var empty = true;
		var changes = {};

		if(fenetreModifier_name.value !== sessions[selectedSession].name) {
			changes.name = fenetreModifier_name.value;
			empty = false;
		}
		if(fenetreModifier_state.value !== sessions[selectedSession].state) {
			changes.state = fenetreModifier_state.value;
			empty = false;
		}
		
		if(!empty)
			ajax_modifierSession(selectedSession, changes);
		else {
			hide(fenetreModifier);
			hide(voile);
		}
	}

	function afficherAutresActions(e, id) {
		selectedSession = id;

		autresActions.style.display = "inline-block";
		autresActions.style.top = e.clientY + "px";
		autresActions.style.left = e.clientX + "px";
	}

	function modifierSession() {
		if(!confirm('ATTENTION ! Modifier la session supprimera les données d\'interactions entre formateur et utilisateurs (posts, résultats de sondages)).'))
			return false;
		
		session_a_modifier.value = selectedSession;
		formModifierSession.submit();
	}
	
	function copierSession(id_session) {
		hide(autresActions);

		var input = prompt('Veuillez indiquer le nom de la nouvelle session');
		
		if(!input)
			return false;
		
		nouveauNom = input;
		selectedSession = id_session;
		ajax_copierSession();
	}

	function restaurerSession(id_session) {
		if(!confirm('Cette session sera déplacé dans Ouvert. Voulez-vous continuer ?'))
			return false;

		ajax_changerStatutSession(id_session, <?php echo OPEN; ?>);
	}

	function deplacerDansCorbeille(id_session) {
		if(!confirm('Cette session sera déplacé dans Corbeille. Voulez-vous continuer ?'))
			return false;

		ajax_changerStatutSession(id_session, <?php echo DUSTBIN; ?>);
	}
	
	function lienSession(index) {
		autresActions.style.display = 'none';

		popupLien.style.display = 'block';
		popupCode.style.display = 'none';
		
		popupLien.style.top = ((getInnerHeight()/2) - (popupLien.offsetHeight/2)) +"px";
		popupLien.style.left = ((getInnerWidth()/2) - (popupLien.offsetWidth/2)) +"px";
		
		if (document.location.hostname == "www.autablo.gretaformation.com")
			inputLien.value = 'http://'+ window.location.host +'/index.php?id='+ index;
		else
			inputLien.value = window.location.host +'/autablo/index.php?id='+ index;
	}
	
	function fermerPopupLien() {
		popupLien.style.display = "none";
	}
	
	function codeSession(index) {
		autresActions.style.display = 'none';

		popupCode.style.display = 'block';
		popupLien.style.display = 'none';
		
		popupCode.style.top = ((getInnerHeight()/2) - (popupCode.offsetHeight/2)) +"px";
		popupCode.style.left = ((getInnerWidth()/2) - (popupCode.offsetWidth/2)) +"px";
		
		inputCode.value = sessions[index]['code'];
	}
	
	function fermerPopupCode() {
		popupCode.style.display = "none";
	}
	
	
	/* AJAX */
	var xhrNameExists = new XMLHttpRequest();
	var xhrCopierSession = new XMLHttpRequest();
	var xhrChangeActiveOrganization = new XMLHttpRequest();
	var xhrChangeViewSessionState = new XMLHttpRequest();
	var xhrChangerStatutSession = new XMLHttpRequest();
	var xhrSupprimerSession = new XMLHttpRequest();
	var xhrModifierSession = new XMLHttpRequest();
	
	
	xhrNameExists.onreadystatechange = function() {
		var xhr = xhrNameExists;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		if(xhr.response === "false") {
			fenetreModifier_name_check.className = "div icon right";
			fenetreModifier_name_check.style.color = "lime";
			fenetreModifier_name_check.innerHTML = "";
		}
		else {
			fenetreModifier_name_check.className = "div icon wrong";
			fenetreModifier_name_check.style.color = "red";
			fenetreModifier_name_check.innerHTML = " Ce nom existe déjà";
		}
	}
	
	xhrCopierSession.onreadystatechange = function() {
		var xhr = xhrCopierSession;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		if(xhr.response === "NAME ALREADY EXISTS")
			alert('Le nom que vous avez spécifié existe déjà.');
		else
			window.location.reload();
	}

	xhrChangeActiveOrganization.onreadystatechange = function() {
		var xhr = xhrChangeActiveOrganization;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrChangeViewSessionState.onreadystatechange = function() {
		var xhr = xhrChangeViewSessionState;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrChangerStatutSession.onreadystatechange = function() {
		var xhr = xhrChangerStatutSession;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}
	
	xhrSupprimerSession.onreadystatechange = function() {
		var xhr = xhrSupprimerSession;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrModifierSession.onreadystatechange = function() {
		var xhr = xhrModifierSession;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		if(xhr.response === "NAME ALREADY EXISTS")
			alert('Le nom que vous avez spécifié existe déjà.');
		else
			window.location.reload();
	}
	
	function ajax_nameExists(nom_session, id_organisation) {
		xhrNameExists.open("POST", "../ajax/nomSessionExist.php", true);
		xhrNameExists.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrNameExists.send("nom="+ encodeURIComponent(nom_session) +"&id_organisation="+ id_organisation);
	}
	
	function ajax_copierSession(){
		var copierInscriptions = confirm("Voulez vous copier les inscriptions ?");
		
		xhrCopierSession.open("POST", "../ajax/copierSession.php", true);
		xhrCopierSession.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrCopierSession.send("id_session_a_copier="+ selectedSession +"&nouveau_nom_session="+ encodeURIComponent(nouveauNom) +"&id_organisation_destination="+ sessions[selectedSession].organization +"&copierInscriptions="+ copierInscriptions);
	};

	function ajax_changeActiveOrganization(organization_id){
		xhrChangeActiveOrganization.open("POST", "../ajax/changeActiveOrganization.php", true);
		xhrChangeActiveOrganization.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrChangeActiveOrganization.send("organization_id="+ organization_id);
	};

	function ajax_changeViewSessionState(state){
		xhrChangeViewSessionState.open("POST", "../ajax/changeViewSessionState.php", true);
		xhrChangeViewSessionState.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrChangeViewSessionState.send("state="+ state);
	};

	function ajax_changerStatutSession(id_session, statut){
		xhrChangerStatutSession.open("POST", "../ajax/changerStatutSession.php", true);
		xhrChangerStatutSession.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrChangerStatutSession.send("id_session="+ id_session +"&statut="+ statut);
	};
	
	function ajax_supprimerSession(id_session) {
		if(!confirm("Etes-vous sur de vouloir supprimer la session "+ sessions[id_session]['name'] +" ? Cette action est irréversible."))
			return false;
		
		xhrSupprimerSession.open("POST", "../ajax/supprimerSession.php", true);
		xhrSupprimerSession.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrSupprimerSession.send("id_session="+ encodeURIComponent(id_session));
	}

	function ajax_modifierSession(id_session, changes){
		xhrModifierSession.open("POST", "../ajax/modifierSession.php", true);
		xhrModifierSession.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrModifierSession.send("id_session="+ id_session +"&changes="+ JSON.stringify(changes));
	};
	
</script>

</body>
</html>