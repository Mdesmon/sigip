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
	$admin = Controls::control_user([SUPERADMIN, ADMINISTRATEUR]);
	$nombre_entrees = 0;
	
	// Gestion des organisations
	$activeOrganization = $_SESSION[APP_NAME]['activeOrganization'];
	$allOrganizations = ($activeOrganization == NO_ACTIVE_ORGANIZATION);

	if($allOrganizations) {
		$users = UsersManager::getAllUsers();
	} else {
		$organization = OrganizationsManager::getOrganizationsHierarchy($activeOrganization);
		$users = UsersManager::getUsersByOrganization($organization, true);
	}
	
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
	
	<title>Gestion utilisateurs</title>
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
						Organisations :
						<select id="selectOrganisation" name="change_activeOrganization">
			';
				
			options_organization(true);

			echo '		</select>
					</li>
				</ul>
			';
		}
		else {
			echo '<div class="space50"></div>';
		}
	?>

	<table class="springtime scrollable">
		<thead>
			<tr>
				<th style="width: 200px;">Prénom Nom</th>
				<th style="width: 170px;">Nom d'utilisateur</th>
				<th style="width: 170px;">Email</th>
				<th style="width: 170px;">Organisation</th>
				<th style="width: 130px;">Statut</th>
				<?php
					if($admin) {
						echo '<th style="width: 150px;">Actions</th>';
					}
				?>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
				foreach ($users as $key => $user) {
					echo '
						<tr>
							<td style="width: 200px;">'. $user->name() .' '. $user->lastname() .'</td>
							<td style="width: 170px;">'. $user->username() .'</td>
							<td style="width: 170px;">'. $user->email() .'</td>
							<td style="width: 170px;">'. OrganizationsManager::getOrganizationName($user->organization()) .'</td>
							<td style="width: 130px;">'. getTypeUser($user->typeUser()) .'</td>
						';
						
						if($admin) {
							echo '<td style="width: 150px;">';

							if(Controls::user_modify($user))
								echo '<div class="btnSpringtime blue" onclick="afficherFenetreModifier('. $key .')" style="width:43px;">Modifier</div> ';
							else
								echo '<div class="btnSpringtime" onclick="afficherFenetreVoir('. $key .')" style="width:43px;">Voir</div> ';
							
							echo '<div class="btnSpringtime red" onclick="ajax_supprimerUntilisateur('. $key .')">Supprimer</div>
								  </td>';
						}
						
						echo '
						</tr>
					';
					
					$nombre_entrees++;
				}
			?>
		</tbody>
	</table>
	
	<div id="nombre_entree">Nombre d'entrées : <?php echo $nombre_entrees; ?></div>
	
	<a href="creation_utilisateurs.php">
		<div id="btn-ajouter" class="btnBlanc2">
			<div class="icon div-indent circle-plus"></div>Ajouter un utilisateur
		</div>
	</a>
	
	<div id="btn-export-csv" class="btnBlanc"><div class="icone-exportCSV"></div>Export CSV</div>

	<div id="voile"></div>

	<div id="fenetreVoir" class="helly" style="display:none">
		<div class="title">Fiche utilisateur</div>
		<div class="space50"></div>
		Nom utilisateur : <span id="fenetreVoir_username"></span>
		<div class="space"></div>
		Prénom : <span id="fenetreVoir_name"></span>
		<div class="space"></div>
		Nom : <span id="fenetreVoir_lastname"></span>
		<div class="space"></div>
		Email : <span id="fenetreVoir_email"></span>
		<div class="space"></div>

		<div class="space25"></div>
		<div id="fenetreVoir_btnFermer" class="btnHelly blue" onclick="hide(fenetreVoir);hide(voile);">Fermer</div>
	</div>
	
	<div id="fenetreModifier" class="helly" style="display:none">
		<div class="title">Paramètres de l'utilisateur</div>
		<div class="space50"></div>
		Nom utilisateur : <span id="fenetreModifier_username"></span>
		<div class="space"></div>
		Prénom : <input id="fenetreModifier_name" class="springtime" type="text" />
		<div class="space"></div>
		Nom : <input id="fenetreModifier_lastName" class="springtime" type="text" />
		<div class="space"></div>
		Email : <input id="fenetreModifier_email" class="springtime" type="text" />
		<div class="space"></div>
		Organisation :
		<select name="etat" id="fenetreModifier_organization">
			<?php 
				if(Controls::inPrimaryOrganization())
					echo '<option value=""></option>';
				
				options_organization();
			?>
		</select>

		<div class="space25"></div>
		<div id="fenetreModifier_btnChangerMdp" class="btnHelly red icon padlock" onclick="hide(fenetreModifier);show(fenetreMdp);centerOnScreen(fenetreMdp);"> Modifier le mot de passe</div>
		<div class="space50"></div>
		<div id="fenetreModifier_btnValider" class="btnSpringtime green icon right-medium" onclick="fenetreModifier_valide();"></div>
		<div id="fenetreModifier_btnAnnuler" class="btnSpringtime red icon wrong-medium" onclick="hide(fenetreModifier);hide(voile);"></div>
	</div>

	<div id="fenetreMdp" class="helly" style="display:none">
		<div class="title">Changer le mot de passe</div>
		<div class="space50"></div>
		Nouveau mot de passe : <input id="fenetreMdp_password" class="springtime" type="text" />
		<div class="space"></div>
		<?php if(MAIL_ENABLED): ?>
			<input type="checkbox" name="" id="fenetreMdp_email" checked=""> Envoyer un mail a l'utilisateur
		<?php endif; ?>
		
		
		<div class="space25"></div>
		<div id="fenetreMdp_btnFermer" class="btnHelly" onclick="hide(fenetreMdp);hide(voile);">Annuler</div>
		<div id="fenetreMdp_btnEnvoyer" class="btnHelly green" onclick="hide(fenetreMdp);hide(voile);ajax_changeUserPassword();">Changer mot de passe</div>
	</div>

<script src="../node_modules/atomjs/atom.js"></script>
<script>
	var tbody = document.getElementById('tbody');
	var selectOrganisation = document.getElementById('selectOrganisation');
	var fenetreModifier = document.getElementById('fenetreModifier');
	var fenetreModifier_name = document.getElementById('fenetreModifier_name');
	var fenetreModifier_lastName = document.getElementById('fenetreModifier_lastName');
	var fenetreModifier_email = document.getElementById('fenetreModifier_email');
	var fenetreModifier_organization = document.getElementById('fenetreModifier_organization');
	var fenetreMdp = document.getElementById('fenetreMdp');
	var fenetreMdp_password = document.getElementById('fenetreMdp_password');
	var fenetreMdp_email = document.getElementById('fenetreMdp_email');
	var fenetreVoir = document.getElementById('fenetreVoir');
	var fenetreVoir_name = document.getElementById('fenetreVoir_name');
	var fenetreVoir_lastName = document.getElementById('fenetreVoir_lastName');
	var fenetreVoir_email = document.getElementById('fenetreVoir_email');
	var voile = document.getElementById('voile');
	var selectedUser = null;
	var users = <?php echo json_encode($users) ?>;

	resize();

	
	onresize = function() {
		resize();
	};

	voile.onmousedown = function(e) {
		e.preventDefault();
	}

	if(selectOrganisation != null) {
		selectOrganisation.onchange = function() {
			ajax_changeActiveOrganization(this.value);
		};
	}

	function resize() {
		tbody.style.maxHeight = (getInnerHeight() - 275) + "px";
	}

	function afficherFenetreVoir(index) {
		var user = users[index];
		
		show(voile);
		show(fenetreVoir);
		centerOnScreen(fenetreVoir);

		fenetreVoir_username.innerHTML = user["username"];
		fenetreVoir_name.innerHTML = user["name"];
		fenetreVoir_lastname.innerHTML = user["lastName"];
		fenetreVoir_email.innerHTML = user['email'];
	}

	function afficherFenetreModifier(index) {
		var user = users[index];
		
		show(voile);
		show(fenetreModifier);
		centerOnScreen(fenetreModifier);

		fenetreModifier_username.innerHTML = user["username"];
		fenetreModifier_name.value = user["name"];
		fenetreModifier_lastName.value = user["lastName"];
		fenetreModifier_email.value = user['email'];

		if(!selectIndexOf(fenetreModifier_organization, user['organization']))
			fenetreModifier_organization.selectedIndex = "0";

		selectedUser = index;
	}

	function fenetreModifier_valide() {
		var empty = true;
		var user = users[selectedUser];
		var changes = {};

		if(fenetreModifier_name.value !== user.name) {
			changes.name = fenetreModifier_name.value;
			empty = false;
		}
		if(fenetreModifier_lastName.value !== user.lastName) {
			changes.lastName = fenetreModifier_lastName.value;
			empty = false;
		}
		if(fenetreModifier_email.value !== user.email) {
			changes.email = fenetreModifier_email.value;
			empty = false;
		}
		if(fenetreModifier_organization.value !== user.organization) {
			if(fenetreModifier_organization.value === "") {
				if(user.organization !== null) {
					changes.organization = fenetreModifier_organization.value;
					empty = false;
				}
			}
			else {
				changes.organization = fenetreModifier_organization.value;
				empty = false;
			}
		}
		
		if(!empty)
			ajax_modifierUtilisateur(user.id, changes);
		else {
			hide(fenetreModifier);
			hide(voile);
		}
	}

	/* AJAX */
	var xhrModifierUtilisateur = new XMLHttpRequest();
	var xhrSupprimerUtilisateur = new XMLHttpRequest();
	var xhrChangeActiveOrganization = new XMLHttpRequest();
	var xhrChangeUserPassword = new XMLHttpRequest();
	
	xhrModifierUtilisateur.onreadystatechange = function() {
		var xhr = xhrModifierUtilisateur;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrSupprimerUtilisateur.onreadystatechange = function() {
		var xhr = xhrSupprimerUtilisateur;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrChangeActiveOrganization.onreadystatechange = function() {
		var xhr = xhrChangeActiveOrganization;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
	    window.location.reload();
	}

	xhrChangeUserPassword.onreadystatechange = function() {
		var xhr = xhrChangeUserPassword;
		
	    if(xhr.readyState != xhr.DONE || xhr.status != 200)	// DONE = 4 ; 200 = OK
	    	return false;
		
		fenetreMdp_password.value = "";
	    alert('Le mot de passe a bien été changé.');
	}

	function ajax_modifierUtilisateur(id_user, changes){
		var xhr = xhrModifierUtilisateur;

		xhr.open("POST", "../ajax/modifyUser.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhr.send("id_user="+ id_user +"&changes="+ JSON.stringify(changes));
	};

	function ajax_supprimerUntilisateur(index) {
		var xhr = xhrSupprimerUtilisateur;
		var user = users[index];

		if(!confirm("Etes-vous sur de vouloir supprimer l'utilisateur "+ user['name'] +" "+ user['lastName'] +" ? Cette action est irréversible."))
			return false;
		
		xhr.open("POST", "../ajax/supprimerUtilisateur.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhr.send("id_utilisateur="+ user['id']);
	}

	function ajax_changeActiveOrganization(organization_id){
		xhrChangeActiveOrganization.open("POST", "../ajax/changeActiveOrganization.php", true);
		xhrChangeActiveOrganization.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrChangeActiveOrganization.send("organization_id="+ organization_id);
	};

	function ajax_changeUserPassword(){
		if(fenetreMdp_email)
			var email = fenetreMdp_email.checked ? 1 : 0;
		else
			var email = 0;

		xhrChangeUserPassword.open("POST", "../ajax/changeUserPassword.php", true);
		xhrChangeUserPassword.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrChangeUserPassword.send("id_user="+ users[selectedUser].id + "&newPassword="+ encodeURIComponent(fenetreMdp_password.value) +"&email="+ email);
	};

</script>

</body>
</html>