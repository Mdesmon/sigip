<?php
/**
 * Gère les inscriptions des utilisateurs aux sessions
 */
class InscriptionsManager
{
	/**
	 * Retourne un array contenant un Session et un User
	 * 
	 * @param int $id_inscription
	 * @return array Array contenant un Session et un User
	 */
	public static function getInscription($id_inscription) {
		$bdd = DB::getInstance();
		$data = NULL;

		// Vérification
		$reponse = $bdd->prepare('SELECT session, utilisateur AS user FROM Inscriptions WHERE id = :id_inscription');
		$reponse->execute(array('id_inscription' => $id_inscription));
		
		$data = $reponse->fetch();

		if($data === FALSE)
			return FALSE;
		
		return array(
			'session' => SessionsManager::getSession($data['session']),
			'user' => UsersManager::getUser($data['user'])
		);
	}
	
	/**
	 * Inscrit un ou plusieurs utilisateurs a une session
	 * 
	 * @param Session $session
	 * @param array $users Accepte soit un User unique ou un tableau d'User
	 * @return array Rapport sur les inscriptions
	 */
	public static function inscrire(Session $session, $users) {
		$bdd = DB::getInstance();
		$report = array(
			'inscrits' => 0,
			'duplicate' => 0,
			'errors' => 0,
			'logs' => []
		);

		if(!is_array($users))
			$users = [$users];


		$reponse = $bdd->prepare("INSERT INTO Inscriptions (utilisateur, session) VALUES (:id_utilisateur, :id_session)");
		
		/* INSCRIPTION */
		foreach ($users as $user) {
			if(self::estInscritAUneSession($session, $user)) {
				$report['duplicate']++;
				continue;
			}

			$executed = $reponse->execute(array('id_session' => $session->id(), 'id_utilisateur' => $user->id()));

			if($executed)
				$report['inscrits']++;
			else {
				$report['errors']++;
				array_push($report['logs'], "L'utilisateur " . $user->name() . " " . $user->lastName() . " n'a pas pu être inscrit");
			}
		}
				
		return $report;
	}
	
	/**
	 * Contrôle qu'un utilisateur est bien inscrit a une session
	 * 
	 * @param Session $session
	 * @param User $user
	 * @return bool
	 */
	public static function estInscritAUneSession(Session $session, User $user) {
		$bdd = DB::getInstance();
		
		$reponse = $bdd->prepare('SELECT id FROM Inscriptions WHERE session = :id_session AND utilisateur = :id_utilisateur');
		$reponse->execute( array('id_session' => $session->id(), 'id_utilisateur' => $user->id()) );
		
		if($reponse->fetch())
			return TRUE;
		else
			return FALSE;
	}
	
	/**
	 * Retourne une liste d'utilisateurs inscrits a une session
	 * 
	 * @param Session $session
	 * @return array
	 */
	public static function listeInscriptionSession(Session $session) {
		$bdd = DB::getInstance();
		$users = [];
		
		$reponse = $bdd->prepare('SELECT utilisateur AS id FROM Inscriptions WHERE session = :id_session');
		$reponse->execute(array('id_session' => $session->id()));

		while ($data = $reponse->fetch(PDO::FETCH_ASSOC)) {
			array_push($users, UsersManager::getUser($data['id']));
		}
		
		return $users;
	}

	/**
	 * Retourne une liste de sessions auquel l'utilisateur cible est inscrit
	 * 
	 * @param User $user
	 * @return array
	 */
	public static function listeInscriptionUser(User $user) {
		$bdd = DB::getInstance();
		$sessions = [];
		
		$reponse = $bdd->prepare('SELECT utilisateur FROM Inscriptions WHERE session = :id_user');
		$reponse->execute(array('id_user' => $user->id()));
		
		while ($data = $reponse->fetch()) {
			array_push($sessions[], new Session($data));
		}
		
		return $sessions;
	}
	
	/**
	 * Retourne une liste d'inscrits sous forme XML pour la session cible
	 * 
	 * @param string $nom_session
	 * @return string 
	 */
	public static function listeInscritsXML($nom_session) {
		$bdd = DB::getInstance();
		
		$xml = new SimpleXMLElement("<inscrits></inscrits>");
		
		$reponse = $bdd->prepare('SELECT Utilisateurs.id, Utilisateurs.nom, Utilisateurs.prenom, Inscriptions.id AS inscription,
								  Utilisateurs.etat, Utilisateurs.session_actuelle
								  FROM Utilisateurs
								  INNER JOIN Inscriptions
								  	ON Utilisateurs.id = Inscriptions.utilisateur
								  INNER JOIN Sessions
								  	ON Sessions.id = Inscriptions.session
								  WHERE Sessions.nom = :nom_session');
		$reponse->execute(array('nom_session' => $nom_session));

		$inscrits = $xml;
		
		while($donnees = $reponse->fetch()) {
			$inscrit = $inscrits->addChild('inscrit');
			$inscrit->addChild('nom', $donnees['nom']);
			$inscrit->addChild('prenom', $donnees['prenom']);
			$inscrit->addChild('id', $donnees['id']);
			$inscrit->addChild('inscription', $donnees['inscription']);
			$inscrit->addChild('etat', $donnees['etat']);
			if($donnees['session_actuelle'] === NULL)
				$session_actuelle = "NULL";
			else
				$session_actuelle = "PAS NULL";
			$inscrit->addChild('session_actuelle', $session_actuelle);
		}
		
		return $xml->asXML();
	}

	/**
	 * Supprime une inscription
	 * 
	 * @param int $id_inscription
	 * @return bool
	 */
	public static function remove($id_inscription) {
		$bdd = DB::getInstance();
		
		// Vérification
		$reponse = $bdd->prepare('SELECT id FROM Inscriptions WHERE id = :id_inscription');
		$reponse->execute(array('id_inscription' => $id_inscription));
		
		if(!$reponse->fetch())
			return FALSE;
		
		// Suppression
		$reponse = $bdd->prepare('DELETE FROM Inscriptions WHERE id = :id_inscription');
		$reponse->execute(array('id_inscription' => $id_inscription));
		
		return TRUE;
	}
}
?>