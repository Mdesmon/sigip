<?php
/**
 * Cette class permet de gérer les sessions de l'application
 */
class SessionsManager
{
	private static $_req =
       'SELECT
            id,
            nom AS name,
            organisation AS organization,
			auteur AS author,
			date_debut AS dateStart,
			date_fin AS dateEnd,
			statut AS state,
			code,
			formateur_connecte AS activeFormer,
			derniere_action AS lastAction,
			version
		FROM Sessions
        ';

	/**
	 * Retourne un objet Session
	 * 
	 * @param int $id
	 * @return Session
     */
	public static function getSession($id) {
		$bdd = DB::getInstance();

		$reponse = $bdd->prepare(self::$_req . ' WHERE id = :id');
		$reponse->execute(array('id' => $id));
		
		if($data = $reponse->fetch(PDO::FETCH_ASSOC))
			return new Session($data);
		
		return FALSE;
	}

	/**
	 * Retourne un objet Session depuis un nom et un id d'organisation
	 * 
	 * @param string $name_session
	 * @param int $id_organization
	 * @return Session
     */
	public static function getSessionByName($name_session, $id_organization) {
		$bdd = DB::getInstance();
		
		if($id_organization === undefined)
			$id_organization = $_SESSION[APP_NAME]['activeOrganization'];

		$reponse = $bdd->prepare(self::$_req . ' WHERE nom = :nom AND organisation = :organisation');
		$reponse->execute(array('nom' => $name_session, 'organisation' => $id_organization));
		
		if($donnees = $reponse->fetch(PDO::FETCH_ASSOC))
			return new Session($data);
		
		return FALSE;
	}

	/**
	 * Retourne un objet Session depuis un code (Chaque sessions est identifiable par un code unique)
	 * 
	 * @param string $code
	 * @return Session
     */
	public static function getSessionByCode($code) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare(self::$_req . ' WHERE code = :code');
		$reponse->execute(array('code' => $code));
		
		if($data = $reponse->fetch(PDO::FETCH_ASSOC))
			return new Session($data);
		
		return "WRONG CODE";
	}

	/**
	 * Retourne les Sessions appartenants a l'organisation cible
	 * 
	 * @param Organization $organization
	 * @param bool $recursive
	 * @return Array Tableau de Session
     */
	public static function getSessionsByOrganization(Organization $organization, $recursive = TRUE) {
        $bdd = DB::getInstance();
        $sessions = [];

        $response = $bdd->prepare(
            self::$_req . '
            WHERE organisation = :organization');
        $response->execute(array('organization' => $organization->id()));
        $data = $response->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $d)
			array_push($sessions, new Session($d));
		
		if($recursive)
            foreach ($organization->subOrganizations() as $subOrganization)
                $sessions = array_merge($sessions, self::getSessionsByOrganization($subOrganization));
        
        return $sessions;
    }

	/**
	 * Retourne les Sessions accessible par l'utilisateur cible
	 * 
	 * @param User $user
	 * @return Array Tableau de Session
     */
	public static function getSessionsByUser(User $user) {
		$bdd = DB::getInstance();
		$sessions = [];
		
		if($user->typeUser() == SUPERADMIN) {
			return SessionsManager::getAllSessions();
		}
		elseif ($user->typeUser() > FORMATEUR) {
			$organization = OrganizationsManager::getOrganizationsHierarchy($user->organization());
			$sessions = SessionsManager::getSessionsByOrganization($organization, TRUE);
		}
		
		// Ajout des Sessions auquelles l'utilisateur est inscrit
		$sessions = array_merge($sessions, InscriptionsManager::listeInscriptionUser($user));
		$sessions = array_unique($sessions, SORT_REGULAR);
        
        return $sessions;
    }

	/**
	 * Retourne la totalité des sessions dans la base de données
	 * 
	 * @return Array Tableau de Session
     */
	public static function getAllSessions() {
		$bdd = DB::getInstance();
		$sessions = [];

		$reponse = $bdd->query(self::$_req);
		
		while($data = $reponse->fetch(PDO::FETCH_ASSOC))
			array_push($sessions, new Session($data));
		
		return $sessions;
	}

	/**
	 * Indique si un id existe
	 * 
	 * @param int $id
	 * @return bool
     */
	public static function idExists($id) {
		$bdd = DB::getInstance();
		$exists = FALSE;
		
		$reponse = $bdd->prepare('SELECT id FROM Sessions WHERE id = :id');
		$reponse->execute(array('id' => $id));
		
		if($reponse->fetch()) {
			$exists = TRUE;
		}
		
		return $exists;
	}

	/**
	 * Indique si un nom de session existe.
	 * Deux sessions ne peuvent pas avoir le même nom s'ils sont dans la même organisation,
	 * cette contrainte est néccessaire pour les identifier de manière unique a partir d'un nom
	 * 
	 * @param string $str
	 * @param int $id_organisation
	 * @return bool
     */
	public static function nameExists($str, $id_organisation = "") {
		$bdd = DB::getInstance();
		$existe = FALSE;
		
		if($id_organisation == "")
			$id_organisation = $_SESSION[APP_NAME]['activeOrganization'];
		
		if($id_organisation == NO_ACTIVE_ORGANIZATION)
			return "NO ACTIVE ORGANIZATION";
		
		$reponse = $bdd->prepare('SELECT nom FROM Sessions WHERE nom = :nom AND organisation = :organisation');
		$reponse->execute(array('nom' => $str, 'organisation' => $id_organisation));
		
		if($reponse->fetch())
			$existe = TRUE;
		
		return $existe;
	}

	/**
	 * Retourne le nom d'une session a partir de son id
	 * 
	 * @param int $id
	 * @return string Nom de la session
     */
	public static function getNameById($id) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare('SELECT nom FROM Sessions WHERE id = :id_session');
		$reponse->execute(array('id_session' => $id));
		
		if($donnees = $reponse->fetch()) {
			return $donnees['nom'];
		}
		
		return FALSE;
	}

	/**
	 * Retourne un id d'organisation depuis un id de session
	 * 
	 * @param int $id Id de la session
	 * @return int id de l'organisation
     */
	public static function getOrganization($id) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare('SELECT organisation FROM Sessions WHERE id = :id');
		$reponse->execute(array('id' => $id_session));
		
		if($donnees = $reponse->fetch())
			return $donnees['organisation'];
		
		return FALSE;
	}

	/**
	 * Génère un code unique pour une session
	 * 
	 * @return string
     */
	public static function getUniqueCode() {
		$code = generatePassword(5);
		
		while (self::codeExists($code)) {
			$code = generatePassword(5);
		}
		
		return $code;
	}

	/**
	 * Indique si un code de session existe
	 * 
	 * @param string $code
	 * @return bool
     */
	public static function codeExists($code) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare('SELECT code FROM Sessions WHERE code = :code collate utf8_bin');
		$reponse->execute(array('code' => $code));
		
		if($reponse->fetch())
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Retourne un string JSON depuis un tableau de Session
	 * 
	 * @param array $sessions Tableau de Session
	 * @return string Représentation JSON des sessions
     */
	public static function serializeSessions($sessions) {
		$json = "{";
		
		foreach ($sessions as $s)
			$json .= $s->id() . ':' . json_encode($s) . ',';
		
		$json = rtrim($json, ',');
		$json .= "}";

		return $json;
	}

	/**
	 * Créer une session dans la base de données
	 * 
	 * @param string $nom_session
	 * @param int $id_organisation
	 * @param int $auteur
	 * @param string $date_debut
	 * @param string $date_fin
	 * @param int $statut
	 * @param string $formateur_connecte
	 * @return string Représentation XML de la session nouvellement créé
     */
	public static function create($nom_session, $id_organisation, $auteur, $date_debut, $date_fin, $statut, $formateur_connecte = "") {
		$bdd = DB::getInstance();
		
		// CREATION
		$reponse = $bdd->prepare(
		   "INSERT INTO Sessions (nom, organisation, auteur, date_debut, date_fin, statut, code, formateur_connecte, version)
			VALUES (:nom_session, :organisation, :auteur, :date_debut, :date_fin, :statut, :code, :formateur_connecte, :version)");
		$reponse->execute(array(
			'nom_session' => $nom_session,
			'organisation' => $id_organisation,
			'auteur' => $auteur,
			'date_debut' => $date_debut,
			'date_fin' => $date_fin,
			'statut' => $statut,
			'code' => self::getUniqueCode(),
			'formateur_connecte' => $formateur_connecte,
			'version' => VERSION
		));
		
		$reponse->closeCursor();
		
		$nouvel_id = $bdd->query("SELECT LAST_INSERT_ID() AS id FROM Sessions")->fetch()['id'];
		
		mkdir("../content/sessions/". $nouvel_id);

		return "<session><id>". $nouvel_id ."</id><nom>". $nom_session ."</nom></session>";
	}

	/**
	 * Renome une session
	 * 
	 * @param Session $session
	 * @param string $newName
	 * @return bool
     */
	public static function rename(Session $session, $newName) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare('UPDATE Sessions SET nom=:nouveau_nom WHERE id=:id_session;');
		return $reponse->execute(array('nouveau_nom' => $newName, 'id_session' => $session->id()));
	}

	/**
	 * Sauvegarde une session.
	 * save contient la structure d'une session (Les questions sans les réponses des apprenants)
	 * 
	 * @param Session $session
	 * @param string $xml
	 * @param bool $autosave
	 * @return void
     */
	public static function save(Session $session, $xml, $autosave = FALSE) {
		$bdd = DB::getInstance();
		
		if($autosave)
			$path = '../content/sessions/'. $session->id() .'/session_autosave.xml';
		else {
			$path = '../content/sessions/'. $session->id() .'/session.xml';
			self::removeSaveState($session);
		}

		$fichier = fopen($path, 'w+');
		fputs($fichier, $xml);
		fclose($fichier);
	}

	/**
	 * Retourne une session au format XML
	 * 
	 * @param Session $session
	 * @return string Représentation XML de la session
     */
	public static function getSave(Session $session) {
		$bdd = DB::getInstance();
		
		$path = '../content/sessions/'. $session->id() .'/session.xml';

		if(!file_exists($path))
			return "NO FILE";
		
		$xml = file_get_contents($path);
		
		return $xml;
	}

	/**
	 * Sauvegarde l'état d'une session.
	 * Une save state contient les réponses des apprenants
	 * 
	 * @param Session $session
	 * @param string $xml_recu
	 * @return bool
     */
	public static function saveState(Session $session, $xml_recu) {
		$nbEcrans = 0;
		$path = "../content/sessions/" . $session->id() . "/";
		
		
		/* TRAITEMENT XML */
		$xml = new SimpleXMLElement($xml_recu);
		
		foreach($xml->children() as $q) {
			$fichier = fopen($path . ($nbEcrans+1) . '.xml', 'w+');
			fputs($fichier, $q->asXML());
			fclose($fichier);
			$nbEcrans+=1;
		}
		
		/* ON EFFACE LES FICHIERS QUI NE SONT PLUS UTILISES */
		for($i=$nbEcrans+1 ; file_exists($path . $i . '.xml') ; $i++) {
			unlink($path . $i . '.xml');
			
			if(file_exists($path . $i)) {
				rrmdir($path . $i);
			}
		}
		
		return true;
	}

	/**
	 * Charge l'état de la session pour le formateur
	 * 
	 * @param Session $session
	 * @return string Représentation XML de l'état de la session
     */
	public static function getSaveState_former(Session $session) {
		$xml = "<questions>";
		
		$path = "../content/sessions/" . $session->id() . "/";

		for($i=1 ; file_exists($path . $i . '.xml') ; $i++) {
			$xml .= file_get_contents($path . $i . '.xml');
		}
		
		$xml .= "</questions>";
		
		return $xml;
	}

	/**
	 * Copie une session
	 * 
	 * @param Session $session_a_copier
	 * @param string $newName
	 * @param Organization $organization_dest
	 * @param bool $copierInscriptions
	 * @param int $statut
	 * @param string $formateur_connecte
	 * @return bool
     */
	public static function copy(Session $session_a_copier, $newName, Organization $organization_dest, $copierInscriptions, $statut, $formateur_connecte = "") {
		if(self::nameExists($newName, $organization_dest->id()))
			return "NAME ALREADY EXISTS";
		
		$id_session_a_copier = $session_a_copier->id();
		$date_debut = date("Y-m-d");
		$date_fin = date("Y-m-d");
		$bdd = DB::getInstance();
		
		
		// CREATION DANS LA BASE
		$reponse = $bdd->prepare("INSERT INTO Sessions (nom, date_debut, date_fin, statut, code, formateur_connecte, organisation, auteur, version)
								  VALUES (:nom_session, :date_debut, :date_fin, :statut, :code, :formateur_connecte, :organisation, :auteur, :version)");
		$reponse->execute(array(
			'nom_session' => $newName,
			'date_debut' => $date_debut,
			'date_fin' => $date_fin,
			'statut' => $statut,
			'code' => generatePassword(5),
			'formateur_connecte' => $formateur_connecte,
			'organisation' => $organization_dest->id(),
			'auteur' => $_SESSION[APP_NAME]['idUser'],
			'version' => VERSION ));
		$reponse->closeCursor();
		
		// RECUPERATION DU NOUVEL ID
		$nouvelId = $bdd->query("SELECT LAST_INSERT_ID() AS id FROM Sessions")->fetch()['id'];
		
		// RECUPERATION DE LA NOUVELLE SESSION
		$nouvelle_session = SessionsManager::getSession($nouvelId);

		
		// COPIE
		
		mkdir("../content/sessions/". $nouvelId);
		
		if(file_exists("../content/sessions/" . $id_session_a_copier . "/session.xml"))
			copy("../content/sessions/" . $id_session_a_copier . "/session.xml", "../content/sessions/" . $nouvelId . "/session.xml");
		if(file_exists("../content/sessions/" . $id_session_a_copier . "/session_autosave.xml"))
			copy("../content/sessions/" . $id_session_a_copier . "/session_autosave.xml", "../content/sessions/" . $nouvelId . "/session_autosave.xml");
		
		for($i = 1 ; file_exists("../content/sessions/" . $id_session_a_copier . "/" . $i . ".xml") ; $i++) {
			copy("../content/sessions/" . $id_session_a_copier . "/" . $i . ".xml", "../content/sessions/" . $nouvelId . "/" . $i . ".xml");
		}

		/* COPIE DES REPONSES APPRENANT */
		ResponsesManager::copy($session_a_copier, $nouvelle_session);
		
		/* COPIE LES INSCRIPTIONS */
		if($copierInscriptions) {
			$utilisateurs = InscriptionsManager::listeInscriptionSession($session_a_copier);
			InscriptionsManager::inscrire($nouvelle_session, $utilisateurs);
		}
		
		return TRUE;
	}

	/**
	 * Supprime une session de la base de données
	 * 
	 * @param Session $session
	 * @return void
     */
	public static function remove(Session $session) {
		$bdd = DB::getInstance();
		$id = $session->id();
		
		$reponse = $bdd->prepare('DELETE FROM Responses WHERE session = :id_session');
		$reponse->execute(array('id_session' => $id));
		
		$reponse = $bdd->prepare('DELETE FROM Inscriptions WHERE session = :id_session');
		$reponse->execute(array('id_session' => $id));

		$reponse = $bdd->prepare('DELETE FROM Swap WHERE session = :id_session');
        $reponse->execute(array('id_session' => $id));
		
		$reponse = $bdd->prepare('DELETE FROM Sessions WHERE id = :id_session');
		$reponse->execute(array('id_session' => $id));
		
		rrmdir("../content/sessions/". $id);
		
		return;
	}

	/**
	 * Supprime la sauvegarde d'état d'une session
	 * 
	 * @param Session $session
	 * @return bool
     */
	public static function removeSaveState(Session $session) {
		$path = "../content/sessions/" . $session->id() . "/";
		
		/* ON EFFACE LES FICHIERS */
		for($i=1 ; file_exists($path . $i . '.xml') ; $i++) {
			unlink($path . $i . '.xml');
			
			if(file_exists($path . $i)) {
				rrmdir($path . $i);
			}
		}

		/* ON EFFACE LES REPONSES DES APPRENANTS DANS LA BASE DE DONNEES */
		ResponsesManager::removeBySession($session);
		
		return true;
	}

	/**
	 * Envoie au formateur par AJAX polling, la mise a jour des réponses de la session courante
	 * 
	 * @param Session $session
	 * @return string Representation XML de la mise a jour incrémentielle
     */
	public static function majIncrementielleFormateur(Session $session) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare(
		   'SELECT Swap.id AS id, Swap.action AS action, Utilisateurs.nom AS nom, Utilisateurs.prenom AS prenom
			FROM Swap
			INNER JOIN Utilisateurs
				ON Swap.utilisateur = Utilisateurs.id
			WHERE Swap.session = :id_session');
		$reponse->execute(array('id_session' => $session->id()));
		
		if(!$donnees = $reponse->fetchAll())
			return FALSE;
		
		/* SUPPRESSION DE LA SWAP */
		$bdd->query('DELETE FROM Swap WHERE session = '. $session->id());
		
		
		/* RÉPONSE POUR LE CLIENT */
		$xml = new SimpleXMLElement('<majIncrementielle></majIncrementielle>');
		$questions = array();
		
		$majIncrementielle = $xml;
		
		for ($i=0; $i < count($donnees) ; $i++) {	// Pour chaque ligne recupèré de ma table SWAP
			$xmlAction = new SimpleXMLElement($donnees[$i]['action']);	// $xmlAction = a la racine de l'XML
			$question = NULL;
			
			/* On repère de quel page il sagit */
			if($xml->question[0]) {	// On vérifie l'existance d'au moins un noeud question
				foreach ($xml->question as $q) {
					if(strval($q['numero']) == strval($xmlAction['numero'])) {
						$question = $q;
						break;
					}
				}
			}
			
			/* Si aucune balise question ne correspond, on la creer */
			if($question === NULL) {
				$question = $xml->addChild('question');
				$question->addAttribute('numero', $xmlAction['numero']);
			}
			
			
			if($xmlAction->getName() === 'reponsePosts') {
				$reponsePosts = $question->addChild('reponsePosts', $xmlAction);	// Récupère le contenu de la balise racine
				$reponsePosts->addAttribute('id_utilisateur', $xmlAction['id_utilisateur']);
				$reponsePosts->addAttribute('auteur', $xmlAction['auteur']);
				if(isset($xmlAction['anonymat']))
					$reponsePosts->addAttribute('anonymat', '');
			}
			else if($xmlAction->getName() === 'reponseCheckbox') {
				$reponseCheckbox = $question->addChild('reponseCheckbox', $xmlAction);	// Récupère le contenu de la balise racine
				$reponseCheckbox->addAttribute('id_utilisateur', $xmlAction['id_utilisateur']);
				if(isset($xmlAction['correcte']))
					$reponseCheckbox->addAttribute('correcte', '');
			}
			else if($xmlAction->getName() === 'reponseRadio') {
				$reponseRadio = $question->addChild('reponseRadio', $xmlAction);	// Récupère le contenu de la balise racine
				$reponseRadio->addAttribute('id_utilisateur', $xmlAction['id_utilisateur']);
				if(isset($xmlAction['correcte']))
					$reponseRadio->addAttribute('correcte', '');
			}
			else if($xmlAction->getName() === 'reponseImageCliquable') {
				$reponseImageCliquable = $question->addChild('reponseImageCliquable', $xmlAction);	// Récupère le contenu de la balise racine
				$reponseImageCliquable->addAttribute('x', $xmlAction['x']);
				$reponseImageCliquable->addAttribute('y', $xmlAction['y']);
				$reponseImageCliquable->addAttribute('id_utilisateur', $xmlAction['id_utilisateur']);
				$reponseImageCliquable->addAttribute('auteur', $xmlAction['auteur']);
			}
			else if($xmlAction->getName() === 'reponseTableau') {
				$reponseTableau = $question->addChild('reponseTableau', $xmlAction);	// Récupère le contenu de la balise racine
				$reponseTableau->addAttribute('ligne', $xmlAction['ligne']);
				$reponseTableau->addAttribute('colonne', $xmlAction['colonne']);
				$reponseTableau->addAttribute('id_utilisateur', $xmlAction['id_utilisateur']);
			}
		}
		
		return $xml->asXML();
	}

	/**
	 * Change le statut d'une session
	 * 
	 * @param Session $session
	 * @param int $statut
	 * @return void
     */
	public static function changeState(Session $session, $statut) {
		$bdd = DB::getInstance();
			
		$reponse = $bdd->prepare('UPDATE Sessions SET statut=:nouveau_statut WHERE id=:id_session;');
		$reponse->execute(array('nouveau_statut' => $statut,'id_session' => $session->id()));

		return;
	}
	

	// A COMPLETER !!!
	// Met à jour le statut des sessions par exemple si un formateur quitte la page de creation/modification et ne revient pas avant l'interval de timeout,
	// alors la session passe de l'état EN MODIFICATION à FERME, afin d'informer que personne n'est en train de modifier la session.
	// Ne pas définir de paramètre met à jour l'intégralité de la table Sessions
	public static function majState($id_utilisateur = NULL) {
		$bdd = DB::getInstance();
		
		if($id_utilisateur !== NULL) {
			$reponse = $bdd->query(
				'SELECT Inscriptions.session AS id_session, Sessions.statut, Sessions.derniere_action
				FROM Sessions
				INNER JOIN Inscriptions
					ON Inscriptions.session = Sessions.id
				WHERE Inscriptions.utilisateur = $id_utilisateur');
		}
		else {
			$reponse = $bdd->query('SELECT Sessions.id AS id_session, Sessions.statut, Sessions.derniere_action FROM Sessions');
		}
		
		
		while($donnees = $reponse->fetch()) {
			if($donnees['statut'] != "EN MODIFICATION")
				continue;
			
			$derniere_action = new DateTime($donnees['derniere_action']);
			
			$interval_derniere_action = $derniere_action->diff(new DateTime);
			$interval_timeout = getIntervalTimeout_connection();
			
			if(!intervalSuperieureA($interval_derniere_action, $interval_timeout))
				continue;
			
			$bdd->query('UPDATE Sessions
							SET formateur_connecte = NULL, statut = "FERME"
							WHERE id = '. $donnees['id_session']);
		}
	}


}	

?>