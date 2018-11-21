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
								echo '<div class="btnSpringtime blue" onclick="affichermenuModifier('. $key .')" style="width:43px;">Modifier</div> ';
							else
								echo '<div class="btnSpringtime" onclick="affichermenuVoir('. $key .')" style="width:43px;">Voir</div> ';
							
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

	<div id="menuVoir" class="menu helly" style="display:none">
		<div class="title">Fiche utilisateur</div>
		<div class="space50"></div>
		Nom utilisateur : <span id="menuVoir_username"></span>
		<div class="space"></div>
		Prénom : <span id="menuVoir_name"></span>
		<div class="space"></div>
		Nom : <span id="menuVoir_lastname"></span>
		<div class="space"></div>
		Email : <span id="menuVoir_email"></span>
		<div class="space"></div>

		<div class="space25"></div>
		<div id="menuVoir_btnFermer" class="btnHelly blue" onclick="hide(menuVoir);hide(voile);">Fermer</div>
	</div>
	
	<div id="menuModifier" class="menu helly" style="display:none">
		<div class="title">Paramètres de l'utilisateur</div>
		<div class="space50"></div>
		Nom utilisateur : <span id="menuModifier_username"></span>
		<div class="space"></div>
		Prénom : <input id="menuModifier_name" class="springtime" type="text" />
		<div class="space"></div>
		Nom : <input id="menuModifier_lastName" class="springtime" type="text" />
		<div class="space"></div>
		Email : <input id="menuModifier_email" class="springtime" type="text" />
		<div class="space"></div>
		Organisation :
		<select name="etat" id="menuModifier_organization">
			<?php 
				if(Controls::inPrimaryOrganization())
					echo '<option value=""></option>';
				
				options_organization();
			?>
		</select>

		<div class="space25"></div>
		<div id="menuModifier_btnChangerMdp" class="btnHelly red icon padlock" onclick="hide(menuModifier);show(menuMdp);centerOnScreen(menuMdp);"> Modifier le mot de passe</div>
		<div class="space50"></div>
		<div id="menuModifier_btnValider" class="btnValider btnSpringtime green icon right-medium" onclick="menuModifier_valide();"></div>
		<div id="menuModifier_btnAnnuler" class="btnAnnuler btnSpringtime red icon wrong-medium" onclick="hide(menuModifier);hide(voile);"></div>
	</div>

	<div id="menuMdp" class="menu helly" style="display:none">
		<div class="title">Changer le mot de passe</div>
		<div class="space50"></div>
		Nouveau mot de passe : <input id="menuMdp_password" class="springtime" type="text" />
		<div class="space"></div>
		<?php if(MAIL_ENABLED): ?>
			<input type="checkbox" name="" id="menuMdp_email" checked=""> Envoyer un mail a l'utilisateur
		<?php endif; ?>
		
		
		<div class="space25"></div>
		<div id="menuMdp_btnFermer" class="btnHelly red" onclick="hide(menuMdp);hide(voile);">Annuler</div>
		<div id="menuMdp_btnEnvoyer" class="btnHelly green" onclick="hide(menuMdp);hide(voile);ajax_changeUserPassword();">Changer mot de passe</div>
	</div>

<script src="../node_modules/atomjs/atom.js"></script>
<script>
	var tbody = document.getElementById('tbody');
	var selectOrganisation = document.getElementById('selectOrganisation');
	var menuModifier = document.getElementById('menuModifier');
	var menuModifier_name = document.getElementById('menuModifier_name');
	var menuModifier_lastName = document.getElementById('menuModifier_lastName');
	var menuModifier_email = document.getElementById('menuModifier_email');
	var menuModifier_organization = document.getElementById('menuModifier_organization');
	var menuMdp = document.getElementById('menuMdp');
	var menuMdp_password = document.getElementById('menuMdp_password');
	var menuMdp_email = document.getElementById('menuMdp_email');
	var menuVoir = document.getElementById('menuVoir');
	var menuVoir_name = document.getElementById('menuVoir_name');
	var menuVoir_lastName = document.getElementById('menuVoir_lastName');
	var menuVoir_email = document.getElementById('menuVoir_email');
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

	function affichermenuVoir(index) {
		var user = users[index];
		
		show(voile);
		show(menuVoir);
		centerOnScreen(menuVoir);

		menuVoir_username.innerHTML = user["username"];
		menuVoir_name.innerHTML = user["name"];
		menuVoir_lastname.innerHTML = user["lastName"];
		menuVoir_email.innerHTML = user['email'];
	}

	function affichermenuModifier(index) {
		var user = users[index];
		
		show(voile);
		show(menuModifier);
		centerOnScreen(menuModifier);

		menuModifier_username.innerHTML = user["username"];
		menuModifier_name.value = user["name"];
		menuModifier_lastName.value = user["lastName"];
		menuModifier_email.value = user['email'];

		if(!selectIndexOf(menuModifier_organization, user['organization']))
			menuModifier_organization.selectedIndex = "0";

		selectedUser = index;
	}

	function menuModifier_valide() {
		var empty = true;
		var user = users[selectedUser];
		var changes = {};

		if(menuModifier_name.value !== user.name) {
			changes.name = menuModifier_name.value;
			empty = false;
		}
		if(menuModifier_lastName.value !== user.lastName) {
			changes.lastName = menuModifier_lastName.value;
			empty = false;
		}
		if(menuModifier_email.value !== user.email) {
			changes.email = menuModifier_email.value;
			empty = false;
		}
		if(menuModifier_organization.value !== user.organization) {
			if(menuModifier_organization.value === "") {
				if(user.organization !== null) {
					changes.organization = menuModifier_organization.value;
					empty = false;
				}
			}
			else {
				changes.organization = menuModifier_organization.value;
				empty = false;
			}
		}
		
		if(!empty)
			ajax_modifierUtilisateur(user.id, changes);
		else {
			hide(menuModifier);
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
		
		menuMdp_password.value = "";
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
		if(menuMdp_email)
			var email = menuMdp_email.checked ? 1 : 0;
		else
			var email = 0;

		xhrChangeUserPassword.open("POST", "../ajax/changeUserPassword.php", true);
		xhrChangeUserPassword.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");	// Pour post il faut changer le type MIME
		xhrChangeUserPassword.send("id_user="+ users[selectedUser].id + "&newPassword="+ encodeURIComponent(menuMdp_password.value) +"&email="+ email);
	};

</script>

</body>
</html>