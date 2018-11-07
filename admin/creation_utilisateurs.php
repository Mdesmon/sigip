<?php
	session_start();
	
	include '../vendor/autoload.php';
	include '../includes/global_include.php';
	include '../includes/envoi_email.php';


	/* SECURITE */
	if(Controls::control_user([APPRENANT])) {
		header('Location: ../index.php');
		exit();
	}
	
	$existeDeja = FALSE;
	$executed = NULL;
	$pirate = FALSE;
	
	$organisation = $_SESSION[APP_NAME]['activeOrganization'];

	if(!empty($_POST)) {
		if(Controls::control_user([APPRENANT])) {	// Controle des type_utilisateur
			$pirate = TRUE;
		}

		if(!$pirate)
		{
			if(UsersManager::usernameExists($_POST['nom_utilisateur'])) {
				$existeDeja = true;
			}
			else {
				if(!isset($_POST['organisation'])) {
					$organisation_id = $_SESSION[APP_NAME]['idOrganization'];
					$organisation = NULL;
				}
				else {
					$organisation_id = $_POST['organisation'];
					$organisation = OrganizationsManager::getOrganization($organisation_id);
				}

				/* Création de l'utilisateur dans la base */
				$user = new User(array(
					'name' => $_POST['prenom'],
					'lastName' => $_POST['nom'],
					'username' => $_POST['nom_utilisateur'],
					'organization' => $organisation_id,
					'email' => $_POST['email'],
					'typeUser' => $_POST['typeUser']
				));
				
				$executed = UsersManager::create($user,	$_POST['password'], true);
				
				LogsManager::addLog("L'utilisateur ". $_POST['nom_utilisateur'] ." a été créée.", USER, $organisation);
			}
		}
		else { // Tentative de piratage ?
			LogsManager::addLog("L'utilisateur a tenté de créer ". $_POST['nom_utilisateur'] ." en tant que super-admin.", INCIDENT, $organization);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
	
	<title>Administration utilisateurs</title>
</head>
<body>
	<?php include '../includes/avatar.php'; ?>
	
	<h1>Gestion des utilisateurs</h1>
	
	<div class="space25"></div>
	
	<?php
		if($executed === TRUE) {
			echo '<div class="bulleDialogue green">L\'utilisateur '. $_POST['prenom'] .' '. $_POST['nom'] .' a bien été crée.</div>';
		} else if($executed === FALSE) {
			echo '<div class="bulleDialogue red">Une erreur est survenu. L\'utilisateur n\'a pas été crée.</div>';
		}
	?>
	
	<form action="creation_utilisateurs.php" method="post">
		<div class="fiche">
			<h2>Ajouter un utilisateur :</h2>
			<div class="space20"></div>
			
			<div class="col-half">
				<div class="space15"></div>
				<div class="col-fifth">
					<label for="nom">Nom :</label>
					<div class="space15"></div>
					<label for="prenom">Prénom :</label>
					<div class="space15"></div>
					<label for="email">Email :</label>
				</div><!--
			 --><div class="col-four-fifth">
					<input id="nom" name="nom" type="text" />
					<div class="space15"></div>
					<input id="prenom" name="prenom" type="text" />
					<div class="space15"></div>
					<input id="email" name="email" type="email" />
				</div><!--
		 --></div><div class="col-half">
		 		<div class="space15"></div>
		 		<?php
					if($existeDeja) { echo '<div class="red">Ce nom d\'utilisateur existe déjà.</div>'; }
				?>
				<div class="col-two-fifth">
					<label for="nom_utilisateur">Nom d'utilisateur :</label>
					<div class="space15"></div>
					<label for="password">Mot de passe :</label>
					<div class="space15"></div>
					<?php
						if(OrganizationsManager::haveSubOrganization($_SESSION[APP_NAME]['idOrganization'])) {
							echo '<label for="nom_utilisateur">Organisation :</label>
								  <div class="space15"></div>';
						}
					?>
					Role :
					<div class="space15"></div>
				</div><!--
			 --><div class="col-three-fifth">
			 		<input id="nom_utilisateur" name="nom_utilisateur" type="text" />
					<div class="space15"></div>
					<input id="password" name="password" type="password" />
					<?php
						if(OrganizationsManager::haveSubOrganization($_SESSION[APP_NAME]['idOrganization'])) {
							echo '
								<div class="space15"></div>
								<select id="organisation" name="organisation">
							';
							
							options_organization();
							
							echo '
								</select>
							';
						}

					?>
					<div class="space15"></div>
					<select id="typeUser" name="typeUser">
						<option value="<?php echo APPRENANT; ?>">Utilisateur</option>
						<option value="<?php echo FORMATEUR; ?>">Formateur</option>
						<!--<option value="3">Superviseur</option>-->
						<?php 
							if(Controls::control_user([ADMINISTRATEUR, SUPERADMIN])) echo '<option value="4">Administrateur</option>';
							if(Controls::control_user([SUPERADMIN])) echo '<option value="5">Super-Admin</option>';
						?>
					</select>
				</div><!--
		 --></div>
		 	
			<div class="space15"></div>
			<div class="alignRight">
				<div id="btnSubmit" class="btnBlanc">Créer l'utilisateur</div>
			</div>
		</div> <!-- Fin main-col -->
	</form>
	
	<a href="gestion_utilisateurs.php">
		<div class="flecheRetour"></div>
	</a>

<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">
	var form = document.getElementsByTagName('form')[0];
	var btnSubmit = document.getElementById('btnSubmit');
	
	btnSubmit.onclick = function() {
		form.submit();
	};
	
</script>

</body>
</html>