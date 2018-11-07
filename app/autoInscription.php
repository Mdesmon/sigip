<?php
	session_start();
	
	include '../vendor/autoload.php';
	include '../includes/global_include.php';
	include '../includes/envoi_email.php';
	
	if(empty($_GET['code']) OR !SessionsManager::codeExists($_GET['code']))
		header('Location: index.php');
	
	$session = SessionsManager::getSessionByCode($_GET['code']);
	$user = NULL;
	$nomUtilisateurVide = NULL;
	$passwordVide = NULL;
	$existeDeja = NULL;
	$passwordCorrect = NULL;
	$nomVide = NULL;
	$prenomVide = NULL;
	$emailVide = NULL;
	$executed = NULL;
	

	if(!empty($_POST)) {
		$bdd = DB::getInstance();
		
		if($_POST['nom_utilisateur'] == "")
			$nomUtilisateurVide = TRUE;
		if($_POST['password'] == "")
			$passwordVide = TRUE;
		
		if(!$nomUtilisateurVide AND !$passwordVide) {
			/* On regarde si l'utilisateur existe */
			$existeDeja = UsersManager::usernameExists($_POST['nom_utilisateur']);
			
			/* Si l'utilisateur existe déjà, on se contente de vérifier son password */
			if($existeDeja) {
				if (Controls::password_verify($_POST['nom_utilisateur'], $_POST['password'])) {
					$user = UsersManager::getUserByUsername($_POST['nom_utilisateur']);
					$passwordCorrect = TRUE;
					
					if( !InscriptionsManager::estInscritAUneSession($session, $user) ) {
						InscriptionsManager::inscrire($session, $user);
					}
				} else {
					$passwordCorrect = FALSE;
				}
			}
			else {	/* Si l'utilisateur n'existe pas... */
				
				/* Controle des saisies de l'utilisateur */
				if(empty($_POST['nom']))
					$nomVide = TRUE;
				if(empty($_POST['prenom']))
					$prenomVide = TRUE;
				if(empty($_POST['email']))
					$emailVide = TRUE;
				
				if(!$nomVide AND !$prenomVide AND !$emailVide) {
					UsersManager::create(
						new User(array(
							'username' => $_POST['nom_utilisateur'],
							'lastName' => $_POST['nom'],
							'name' => $_POST['prenom'],
							'email' => $_POST['email'],
							'typeUser' => APPRENANT
						)),
						$_POST['password'],
						true
					);
					$user = UsersManager::getUserByUsername($_POST['nom_utilisateur']);
					
					/* Inscription session */
					InscriptionsManager::inscrire($session, $user);
				}
			}
			
			if($passwordCorrect OR (!$existeDeja AND !$nomVide AND !$prenomVide AND !$emailVide)) {
				/* Connexion de l'utilisateur */
				Controls::connect($user);
				
				/* Redirection */
				header('Location: session_apprenant.php?session_a_charger=' . $session->id());
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php include '../includes/head.html'; ?>

	<link rel="stylesheet" type="text/css" href="../css/gestion.css">
	
	<title>Auto inscription</title>
</head>
<body>
	<h1>Auto inscription</h1>
	
	<div class="space25"></div>
	
	<?php
		if($nomUtilisateurVide) {
			echo '<div class="bulleDialogue red">Vous devez entrer un nom d\'utilisateur.</div>';
		}
		else if($passwordVide) {
			echo '<div class="bulleDialogue red">Vous devez entrer un mot de passe.</div>';
		}
		else if($passwordCorrect === FALSE) {
			echo '<div class="bulleDialogue red">Le mot de passe ne correspond pas avec le nom d\'utilisateur.</div>';
		}
		else if($nomVide) {
			echo '<div class="bulleDialogue red">Le nom n\'a pas été saisie.</div>';
		}
		else if($prenomVide) {
			echo '<div class="bulleDialogue red">Le prénom n\'a pas été saisie.</div>';
		}
		else if($emailVide) {
			echo '<div class="bulleDialogue red">L\'email n\'a pas été saisie.</div>';
		}
		else if($executed === FALSE) {
			echo '<div class="bulleDialogue red">Une erreur est survenu. L\'utilisateur n\'a pas été crée.</div>';
		}
	?>
	
	<form action="autoInscription.php?code=<?php echo $_GET['code'] ?>" method="post">
		<div class="fiche">
			<div class="titre2">Inscrivez vous :</div>
			<div class="space20"></div>
			
			<div class="col-half">
				<div class="col-four-fifth categorie">IDENTITE</div>
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
		 		<div class="categorie">PROFIL</div>
		 		<div class="space15"></div>
				<div class="col-two-fifth">
					<label for="nom_utilisateur">Nom d'utilisateur :</label>
					<div class="space15"></div>
					<label for="password">Mot de passe :</label>
				</div><!--
			 --><div class="col-three-fifth">
			 		<input id="nom_utilisateur" name="nom_utilisateur" type="text" />
					<div class="space15"></div>
					<input id="password" name="password" type="password" />
					<div class="space15"></div>
				</div><!--
		 --></div>
		 	
			<div class="space15"></div>
			<div class="alignRight">
				<div id="btnSubmit" class="btnBlanc">S'inscrire et accéder à la session</div>
			</div>
		</div> <!-- Fin main-col -->
	</form>
	
<script type="text/javascript">
	var form = document.getElementsByTagName('form')[0];
	var btnSubmit = document.getElementById('btnSubmit');
	
	btnSubmit.onclick = function() {
		form.submit();
	};
	
</script>

</body>
</html>