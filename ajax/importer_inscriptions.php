<?php
	session_start();
	
	include '../vendor/autoload.php';
	include '../includes/global_include.php';
	include '../includes/envoi_email.php';
	header("Content-type: text/plain; charset=UTF-8");

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Importer inscriptions) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

	if(Controls::control_user([APPRENANT])) {
        echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Importer inscriptions) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}
	
	
	
	$bdd = DB::getInstance();
	$fin_de_ligne = "\r\n";
	$separateur = ";";
	$debut_header = "prenom";	// Si le fichier commence pas cette chaine, alors on considére que la première ligne n'est qu'un entete
	$pos = 0;
	$ligneActuelle = 1;
	$endOfFile = false;
	$messages = array();
	if(empty($_POST['fichierInscriptions'])) {
		$messages[] = "Erreur: Les données sont inexistantes.";
	}
	else {
		$fichier = $_POST['fichierInscriptions'];
	}
	
	
	if(!empty($_POST['fichierInscriptions'])) {
		// Controles du fichier
		if(strtolower(substr($fichier, 0, strlen($debut_header))) == $debut_header) {
			$messages[] = "header trouvé";
			passerLaLigne($fichier, $pos, $ligneActuelle, $fin_de_ligne, $endOfFile);
		}
		
		while(!$endOfFile) {
			traiterLaLigneSuivante($fichier, $pos, $ligneActuelle, $fin_de_ligne, $separateur, $bdd, $endOfFile, $messages);
		}
	}
	
	foreach ($messages as $messageErreur) {
		echo $messageErreur ."\r\n";
	}
	
	
	/* FUNCTIONS */
	
	function passerLaLigne(&$fichier, &$pos, &$ligneActuelle, &$fin_de_ligne, &$endOfFile) {
		$fichier = substr($fichier, $pos);	// Supprime la partie déjà traité
		
		if($pos = strpos($fichier, $fin_de_ligne)) {
			$pos += 2;
			$ligneActuelle++;
			return true;
		}
		else {
			$pos = -1;
			$endOfFile = true;
			return false;
		}
	}
	
	function traiterLaLigneSuivante(&$fichier, &$pos, &$ligneActuelle, &$fin_de_ligne, &$separateur, $bdd, &$endOfFile, &$messages) {
		$ligne = "";
		$values = array('nom' => '', 'prenom' => '', 'nom_utilisateur' => '', 'password' => '', 'email' => '');
		
		$fichier = substr($fichier, $pos);	// Supprime la partie déjà traité
		$pos = strpos($fichier, $fin_de_ligne);
		
		if($pos !== FALSE) {
			$ligne = substr($fichier, 0, $pos);
			$pos += 2;
		}
		else {	// Si on ne trouve pas les caractères \r\n, alors on est a la dernière ligne
			$pos = -1;
			$endOfFile = true;
			$ligne = $fichier;
		}
		
		if($ligne != "") {
			$ligne = explode($separateur, $ligne);
			
			if(count($ligne) >= 5) {
				$values['prenom'] = $ligne[0];
				$values['nom'] = $ligne[1];
				$values['nom_utilisateur'] = $ligne[2];
				$values['password'] = $ligne[3];
				$values['email'] = $ligne[4];
				
				inscrire($values, $ligneActuelle, $bdd, $messages);
			}
			else if(count($ligne) < 5) {
				$messages[] = "Le nombre d'argument est insuffisant à la ligne ". $ligneActuelle .". (". count($ligne) .")";
			}
		} else {
			$messages[] = "La ligne ". $ligneActuelle ." est vide.";
		}
		
		$ligneActuelle++;
	}
	
	function inscrire($values, $ligneActuelle, $bdd, &$messages) {
		if($values['nom_utilisateur'] != "") {
			/* Créer l'utilisateur avec toutes les vérifications */
			UsersManager::create(
				new User(array(
					'username' => $values['nom_utilisateur'],
					'lastName' => $values['nom'],
					'name' => $values['prenom'],
					'email' => $values['email'],
					'typeUser' => APPRENANT
				)),
				$values['password'],
				true
			);
			
			/* Inscrit l'utilisateur a la session avec toutes les vérifications */
			$session = SessionsManager::getSession($_POST['id_session']);
			$user = UsersManager::getUserByUsername($values['nom_utilisateur']);
			InscriptionsManager::inscrire($session, $user);
		}
		else {	// Pas de nom_utilisateur
			$messages[] = "nom d'utilisateur vide à la ligne ". $ligneActuelle .". Cette entrée sera ignoré.";
		}
	}
	
?>