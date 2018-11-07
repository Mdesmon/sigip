<?php
	include '../vendor/autoload.php';
	include '../includes/global_include.php';
	include '../includes/envoi_email.php';

	$formulaire_recu = (!empty($_POST));
	$formulaire_valide = (!empty($_POST['username']) AND !empty($_POST['password']));
	$bdd = DB::getInstance();

	$DB_NAME = substr(DB_DSN, strpos(DB_DSN, "dbname=")+7);
	$DB_NAME = substr($DB_NAME, 0, strpos($DB_NAME, ';'));
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>
	
	<link rel="stylesheet" type="text/css" href="../css/gestion.css">

	<title>Installation <?php echo APP_NAME; ?></title>

	<style>
		h1 {
			font-size: 50px;
			margin: 30px;
		}
		h2 {
			font-size: 40px;
			margin: 20px;
		}
		#form {
			width: 350px;
			margin: auto;
			background-color: rgba(0, 0, 0, .6);
			border-radius: 5px;
			display: block;
		}
		#form label {
			display: block;
			text-align: right;
			padding-right: 5px;
		}
		#form .col {
			line-height: 27px;
		}
		.p {
			font-family: Calibri, Arial;
			font-size: 22px;
			text-align: center;
			padding: 5px 15px;
			background-color: rgba(0, 0, 0, .6);
			border-radius: 5px;
			display: inline;
		}
	</style>
</head>
<body>
	<h1>Installation de l'application <?php echo APP_NAME; ?></h1>

	<div class="center">
		<?php
			if(!$formulaire_recu) {
				echo '<h2>Installation de la base de données</h2>';

				// STRUCTURE DE LA BASE DE DONNEES
				$reponse = $bdd->query('SELECT COUNT(DISTINCT table_name) AS nbTables FROM information_schema.columns WHERE table_schema = "' . $DB_NAME . '"');
				$donnees = $reponse->fetch();

				if($donnees['nbTables'] > 0) {
					echo '<div class="p">La base '. $DB_NAME .' n\'est pas vide. La structure ne sera pas importé.</div>
						<div class="space25"></div>';
					$bdd_ok = TRUE;
				}
				else {
					echo '<div class="p">La base '. $DB_NAME .' a été découverte. Importation de la structure...</div>
						<div class="space25"></div>';
					
					if(file_exists('sql/install.sql')) {
						try {
							$liste_instructions = explode(';', file_get_contents('sql/install.sql'));
							
							foreach($liste_instructions as $instruction) {
								$bdd->query($instruction);
							}

							$bdd_ok = TRUE;
						}
						catch(Exception $e)
						{
							$bdd_ok = FALSE;
							echo '<div class="p red">Erreur : '. $e->getMessage() .'</div>
								<div class="space25"></div>';
						}
					}
					else {
						$bdd_ok = FALSE;
						echo '<div class="p red">Le fichier install.sql n\'existe pas. Impossible d\'importer la structure.</div>
							<div class="space25"></div>';
					}

					

					if($bdd_ok) {
						echo '<div class="p green">Structure de la base de donnees importé avec succes !</div>
							<div class="space25"></div>';
					}
					else {
						echo '<div class="p red">Echec de l\'importation.</div>
							<div class="space25"></div>';
					}
				}

				// AJOUT DU PREMIER SUPER-ADMIN
				if($bdd_ok) {
					echo '<h2>Création d\'un super-admin</h2>';

					$reponse = $bdd->query('SELECT COUNT(*) AS nb FROM Utilisateurs');
					$donnees = $reponse->fetch();

					if($donnees['nb'] > 0): ?>
						<div class="p">La table Utilisateurs n'est pas vide. Aucun super-admin ne sera créé.</div>
						<div class="space25"></div>';
					<?php else: ?>
						<!-- Affichage du formulaire -->
						<form id="form" method="post" action="installer.php">
							<div class="space20"></div>

							<div class="col col-two-fifth">
								<label for="inputNomUtilisateur">Nom utilisateur :</label>
								<div class="space"></div>
								<label for="inputMdp">Mot de passe :</label>
								<div class="space"></div>
								<label for="inputNom">Nom :</label>
								<div class="space"></div>
								<label for="inputPrenom">Prenom :</label>
								<div class="space"></div>
								<label for="inputEmail">Email :</label>
								<div class="space"></div>
							</div><!--
						--><div class="col col-three-fifth alignLeft">
								<input type="text" id="inputNomUtilisateur" name="username" />
								<div class="space"></div>
								<input type="text" id="inputMdp" name="password" />
								<div class="space"></div>
								<input type="text" id="inputNom" name="nom" />
								<div class="space"></div>
								<input type="text" id="inputPrenom" name="prenom" />
								<div class="space"></div>
								<input type="email"  id="inputEmail" name="email" />
								<div class="space"></div>
							</div>
							
							<div id="btnAjouterSuperadmin" class="btnBlanc" onclick="ajouterSuperadmin()">
								Creer le super-admin
							</div>

							<div class="space20"></div>
						</form>
					<?php endif;
				}
			}	// FIN DE IF !$formulaire_recu
			else {	// SI UN FORMULAIRE A ETE RECU
				if(!$formulaire_valide) {
					echo '<div class="p red">Les données reçues sont insuffisantes</div>';
				}
				else {
					$executed = creerUtilisateur($_POST['nom'], $_POST['prenom'], $_POST['username'], $_POST['password'], NULL, $_POST['email'], SUPERADMIN);

					if($executed) // La requête s'est correctement déroulé
						echo '<div class="p green">Le super-admin à été enregistré avec succees !</div>';
					else
						echo '<div class="p red">Une erreur s\'est produite. Le super-admin n\'a pas été enregistré</div>';
				}
			}
		?>

	</div>

	<div class="space"></div>
	
<script type="text/javascript">
	var form = document.getElementById('form');
	var btnAjouterSuperadmin = document.getElementById('btnAjouterSuperadmin');
	var inputNomUtilisateur = document.getElementById('inputNomUtilisateur');
	var inputMdp = document.getElementById('inputMdp');


	if(btnAjouterSuperadmin) {
		btnAjouterSuperadmin.onclick = function() {
			if(inputNomUtilisateur.value.trim() == "") {
				alert("Nom utilisateur doit être renseigné");
				return false;
			}
			else if(inputMdp.value == "") {
				alert("Le mot de passe doit être renseigné");
				return false;
			}

			form.submit();
		};
	}
	



</script>

</body>
</html>
<?php

function creerUtilisateur($nom, $prenom, $username, $password, $organisation, $email, $type_utilisateur) {
		$bdd = DB::getInstance();
		
		try {
			$user = new User(array(
				'name' => $prenom,
				'lastName' => $nom,
				'username' => $username,
				'organization' => $organisation,
				'email' => $email,
				'typeUser' => $type_utilisateur
			));

			$executed = UsersManager::create($user, $password, true);	// Créer le super-admin et lui envoi un mail
		}
		catch(Exception $e) {
			echo '<div class="p red">Erreur : '. $e->getMessage() .'</div>
				  <div class="space25"></div>';
			
			$executed = FALSE;
		}

		return $executed;
	}

?>