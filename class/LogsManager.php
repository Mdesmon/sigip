<?php
/**
 * Gère les logs
 */
class LogsManager
{
	/**
	 * Ajoute un log
	 * 
	 * @param string $message
	 * @param int $type Type de l'erreur
	 * @param Organization $organization
	 * @return bool
	 */
	public static function addLog($message, $type, Organization $organization = NULL) {
		$bdd = DB::getInstance();

		if($organization === NULL)
			$organization = "NULL";
		else
			$organization = $organization->id();

		$reponse = $bdd->prepare(
		   'INSERT INTO Logs (commentaire, type, utilisateur, organisation, datetime_creation)
			VALUES (:commentaire, :type, :user,'. $organization .',"'. date('Y-m-d H:i:s') .'")');
		
		$executed = $reponse->execute(array(
			'commentaire' => $message,
			'type' => $type,
			'user' => isset($_SESSION[APP_NAME]['idUser']) ? $_SESSION[APP_NAME]['idUser'] : NULL
		));
		
		return $executed;
	}

	/**
	 * Retourne une liste de logs correspondant a l'organisation cible
	 * 
	 * @param Organization $organization
	 * @return array
	 */
	public static function getLogsByOrganization($organization) {
		$bdd = DB::getInstance();
		$logs = NULL;

		if($organization === NULL OR $organization === -1) {
			$reponse = $bdd->query(
			   'SELECT Logs.id, Utilisateurs.nom_utilisateur, Organisations.nom AS nom_organisation, Types_log.type, Logs.datetime_creation AS datetime, Logs.commentaire
				FROM Logs
				LEFT JOIN Utilisateurs ON Logs.utilisateur = Utilisateurs.id
				INNER JOIN Types_log ON Logs.type = Types_log.id
				LEFT JOIN Organisations ON Logs.organisation = Organisations.id
				ORDER BY datetime DESC;
			');
		}
		else {
			$reponse = $bdd->prepare(
			   'SELECT Logs.id, Utilisateurs.nom_utilisateur, Organisations.nom AS nom_organisation, Types_log.type, Logs.datetime_creation AS datetime, Logs.commentaire
				FROM Logs
				LEFT JOIN Utilisateurs ON Logs.utilisateur = Utilisateurs.id
				INNER JOIN Types_log ON Logs.type = Types_log.id
				LEFT JOIN Organisations ON Logs.organisation = Organisations.id
				WHERE (Logs.organisation = :organisation OR parent = :organisation)
				ORDER BY datetime DESC;
			');
			$reponse->execute(array('organisation' => $organization));
		}

		$logs = $reponse->fetchAll(PDO::FETCH_ASSOC);

		return $logs;
	}

	/**
	 * Retourne une liste d'ids de logs correspondant a l'organisation cible
	 * 
	 * @param Organization $organization
	 * @return array Array d'ids
	 */
    public static function getLogsIdByOrganization(Organization $organization) {
        $bdd = DB::getInstance();
        $id_array = [];
		
		$reponse = $bdd->prepare('SELECT id FROM Logs WHERE organisation = :id_organisation');
        $reponse->execute(array('id_organisation' => $organization->id()));
        
        while ($data = $reponse->fetch()) {
			array_push($id_array, $data['id']);
		}
		
		return $id_array;
    }

	/**
	 * Supprime un log
	 * 
	 * @param int $id
	 * @return bool
	 */
    public static function remove($id) {
		$bdd = DB::getInstance();
				
		// Suppression
		$reponse = $bdd->prepare('DELETE FROM Logs WHERE id = :id');
		$reponse->execute(array('id' => $id));
		
		return TRUE;
    }
}

?>