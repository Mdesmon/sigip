<?php
/**
 * Gère toutes les opérations qui touchent a la sécurité
 * Permet d'effectuer les contrôles de sécurité comme les droits d'accès, les droits de modifications utilisateur, etc...
 */
class Controls
{
	/**
     * Connecte l'utilisateur et remplie ses variables de SESSION
     * 
     * @param User $user Utilisateur cible
     * @return bool
     */
	public static function connect(User $user) {
        $bdd = DB::getInstance();
		
		self::disconnect();	// Supprime les variables dans $_SESSION[APP_NAME] par sécurité

		$_SESSION[APP_NAME]['idUser'] = $user->id();
		$_SESSION[APP_NAME]['username'] = $user->username();
		$_SESSION[APP_NAME]['lastName'] = $user->lastName();
		$_SESSION[APP_NAME]['name'] = $user->name();
		$_SESSION[APP_NAME]['typeUser'] = $user->typeUser();

		$_SESSION[APP_NAME]['idOrganization'] = $user->organization();
		$_SESSION[APP_NAME]['activeOrganization'] = $user->organization() ? intval($user->organization()) : NO_ACTIVE_ORGANIZATION;
		$_SESSION[APP_NAME]['viewSessionState'] = OPEN;

		return true;
    }
	
	/**
     * Déconnecte l'utilisateur et supprime par sécurité ses variables de SESSION
     */
    public static function disconnect() {
        unset($_SESSION[APP_NAME]);
    }

	/**
     * Indique si l'utilisateur est connecté
	 * 
	 * @return bool
     */
	public static function isConnected() {
		return (isset($_SESSION[APP_NAME]['username']));
	}

	/**
	 * Renvoie un objet User qui correspond a l'utilisateur connecté
	 * 
	 * @return User
     */
	public static function getConnectedUser() {
		$userId = $_SESSION[APP_NAME]['idUser'];

		return UsersManager::getUser($userId);
	}

	/**
	 * Renvoie un objet Organization qui correspond a l'organisation de l'utilisateur
	 * 
	 * @return Organization
     */
	public static function getUserOrganization() {
		$organizationId = $_SESSION[APP_NAME]['idOrganization'];

		return OrganizationsManager::getOrganization($organizationId);
	}

	/**
	 * Renvoie un objet Organization qui correspond a l'organisation active de l'utilisateur
	 * 
	 * @return Organization
     */
	public static function getActiveOrganization() {
		$organizationId = $_SESSION[APP_NAME]['activeOrganization'];

		return OrganizationsManager::getOrganization($organizationId);
	}

	/**
	 * Contrôle que l'utilisateur fait bien partie des types utilisateurs autorisées
	 * 
	 * @param array tableau de constantes des types utilisateurs autorisées
	 * @return bool
     */
    public static function control_user($types_users) {
		if(!self::isConnected())
			$types_users = [APPRENANT];	// Par sécurité, en l'abscence de connexion, control_user() se comporte de manière à empêcher un accès administrateur

		return in_array($_SESSION[APP_NAME]['typeUser'], $types_users);
	}

	/**
	 * Contrôle qu'un mot de passe correspond bien à un username
	 * 
	 * @param string $username
	 * @param string $password
	 * @return string
     */
	public static function password_verify($username, $password) {
		$bdd = DB::getInstance();

		if(!UsersManager::usernameExists($username))
			return "WRONG USERNAME";

		$response = $bdd->prepare(
		   'SELECT mdp AS password_hash
			FROM Utilisateurs
			WHERE nom_utilisateur = :username');
		$response->execute(array('username' => $username));
		$data = $response->fetch(PDO::FETCH_ASSOC);

		return password_verify($password, $data['password_hash']) ? "OK" : "WRONG PASSWORD";
	}

	/**
	 * Contrôle que l'utilisateur connecté est bien autorisé a modifier l'utilisateur cible
	 * 
	 * @param User $targetUser
	 * @return bool
     */
	public static function user_modify(User $targetUser) {
		$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

		if($user->typeUser() == SUPERADMIN) {
			if($user->organization() === NULL)
				return TRUE;
			elseif( $targetUser->organization() === $user->organization() OR in_array($targetUser->organization(), OrganizationsManager::getChildsIds($user->organization())) )
				return TRUE;
		}
			
		elseif (
			$targetUser->typeUser() < ADMINISTRATEUR
			AND $user->typeUser() == ADMINISTRATEUR
			AND ( $targetUser->organization() === $user->organization() OR in_array($targetUser->organization(), OrganizationsManager::getChildsIds($user->organization())) )
		)
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Contrôle que l'utilisateur passé en argument est bien autorisé a modifier la session cible
	 * 
	 * @param Session $session
	 * @param User $user
	 * @return bool
     */
	public static function session_modify(Session $session, User $user) {
		if($user->typeUser() == SUPERADMIN)
			return TRUE;
		
		if($session->author() == $user->id())
			return TRUE;
		
		if (
			$user->typeUser() == ADMINISTRATEUR
			AND ( $user->organization() === $session->organization() OR in_array($session->organization(), OrganizationsManager::getChildsIds($user->organization())) )
		)
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Contrôle que l'utilisateur passé en argument est bien autorisé a animer la session cible
	 * 
	 * @param Session $session
	 * @param User $user
	 * @return bool
     */
	public static function session_trainerAccess(Session $session, User $user) {
		if($user->typeUser() == SUPERADMIN)
			return TRUE;
		
		if($session->author() == $user->id())
			return TRUE;
		
		if (
			$user->typeUser() > APPRENANT
			AND ( $user->organization() === $session->organization() OR in_array($session->organization(), OrganizationsManager::getChildsIds($user->organization())) )
		)
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Contrôle que l'utilisateur passé en argument est bien autorisé a entrer dans la session cible en tant qu'apprenant
	 * 
	 * @param Session $session
	 * @param User $user
	 * @return bool
     */
	public static function session_access(Session $session, User $user) {
		if(self::session_modify($session, $user))
			return TRUE;

		if(InscriptionsManager::estInscritAUneSession($session, $user))
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Indique si l'utilisateur se situe dans l'organisation primaire (premier niveau d'organisation)
	 * 
	 * @return bool
     */
	public static function inPrimaryOrganization() {
		return $_SESSION[APP_NAME]['idOrganization'] === NULL;
	}

	/**
	 * Indique si l'utilisateur peut renomer, modifier les sous-organisations, et modifier les sessions qui ne lui appartiennent pas dans une organisation spécifique
	 * 
	 * @param Organization $organization
	 * @param User $user
	 * @return bool
     */
	public static function organization_modify(Organization $organization, User $user) {
		if($user->typeUser() == SUPERADMIN)
			return TRUE;
		
		if (
			$user->typeUser() == ADMINISTRATEUR
			AND ( $user->organization() === $organization->id() OR in_array($organization->id(), OrganizationsManager::getChildsIds($user->organization())) )
		)
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Indique si l'utilisateur peut supprimer l'organisation (le droit modify n'implique pas forcément un droit de suppression)
	 * 
	 * @param Organization $organization
	 * @param User $user
	 * @return bool
     */
	public static function organization_remove(Organization $organization, User $user) {
		return (
			Controls::organization_modify($organization, $user)
			AND $user->organization() != $organization->id() // Un admin ne doit pas pouvoir supprimer l'organisation a laquelle il est directement attaché
		);
	}

	/**
	 * Indique si l'utilisateur peur voir les sessions d'une organisation spécifique (sans prendre en compte les inscriptions)
	 * 
	 * @param Organization $organization
	 * @param User $user
	 * @return bool
     */
	public static function organization_access(Organization $organization, User $user) {
		if($user->typeUser() == SUPERADMIN)
			return TRUE;
		
		if(
			$user->typeUser() > APPRENANT
			AND ( $user->organization() == $organization->id() OR in_array($organization->id(), OrganizationsManager::getChildsIds($user->organization())) )
		)
			return TRUE;
		
		return FALSE;
	}

	/**
	 * Change l'organisation active pour l'utilisateur connecté
	 * 
	 * @param int $organization_id
	 * @return void
     */
	public static function changeActiveOrganization($organization_id) {
		$_SESSION[APP_NAME]['activeOrganization'] = intval($organization_id);
	}

	/**
	 * Change le mode de vue des sessions pour l'utilisateur connecté (OUVERT, FERME, ARCHIVE...)
	 * 
	 * @param int $state
	 * @return void
     */
	public static function changeViewSessionState($state) {
		$_SESSION[APP_NAME]['viewSessionState'] = intval($state);
	}

}

?>