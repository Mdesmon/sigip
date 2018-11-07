<?php
/**
 * Gère les class Organization et OrganizationsHierarchy
 */ 
class OrganizationsManager
{
    private static $_req =
       'SELECT
            id,
            nom AS name,
            parent
        FROM Organisations
        ';

    /**
	 * Retourne un objet Organization
	 * 
	 * @param int $id
	 * @return Organization
     */
    public static function getOrganization($id) {
        $bdd = DB::getInstance();
        
        if(!self::idExists($id))
            return FALSE;

        $response = $bdd->prepare(
            self::$_req . '
            WHERE id = :id');
        
        $response->execute(array('id' => $id));
        $data = $response->fetch(PDO::FETCH_ASSOC);

        return new Organization($data);
    }
    
    /**
	 * Retourne un objet Organization depuis un nom
	 * 
	 * @param string $name
	 * @return Organization
     */
    public static function getOrganizationByName($name) {
        $bdd = DB::getInstance();
            
        if(!self::nameExists($name))
            return "WRONG NAME";

        $response = $bdd->prepare(
            self::$_req . '
            WHERE nom = :name');
        $response->execute(array('name' => $name));
        $data = $response->fetch(PDO::FETCH_ASSOC);
        
        return new Organization($data);
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
        
        $reponse = $bdd->prepare('SELECT id FROM Organisations WHERE id = :id');
        $reponse->execute(array('id' => $id));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
	 * Indique si un nom existe
	 * 
	 * @param string $name
	 * @return bool
     */
    public static function nameExists($name) {
        $bdd = DB::getInstance();
        
        $exists = FALSE;
        
        $reponse = $bdd->prepare('SELECT nom FROM Organisations WHERE nom = :name');
        $reponse->execute(array('name' => $name));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
	 * Retourne un nom d'organisation depuis un id
	 * 
	 * @param int $id
	 * @return string Nom d'organisation correspondant a l'id
     */
    public static function getOrganizationName($id) {
        $bdd = DB::getInstance();
        
        $reponse = $bdd->prepare('SELECT nom FROM Organisations WHERE id = :id');
        $reponse->execute(array('id' => $id));
        
        return $reponse->fetch()['nom'];
    }

    /**
	 * Creer une organisation dans la base de données
	 * 
	 * @param Organization $organization
	 * @return int
     */
    public static function create(Organization $organization) {
        $bdd = DB::getInstance();
        
        $reponse = $bdd->prepare('INSERT INTO Organisations (nom, parent) VALUES (:nom, :parent)');
        return $reponse->execute(array(
            'nom' => $organization->name(),
            'parent' => $organization->parent()
        ));
    }

    /**
	 * Renomme une organisation dans la base de données
	 * 
	 * @param Organization $organization
	 * @param string $newName
	 * @return int
     */
    public static function rename(Organization $organization, $newName) {
        $bdd = DB::getInstance();
        
        if(self::nameExists($newName))
            return "NAME ALREADY EXISTS";

        $reponse = $bdd->prepare('UPDATE Organisations SET nom=:nouveau_nom WHERE id=:id_organisation;');
        return $reponse->execute(array('nouveau_nom' => $newName, 'id_organisation' => $organization->id()));
    }

    /**
	 * Supprime une organisation dans la base de données
	 * 
	 * @param OrganizationsHierarchy $organization
	 * @return array Raport sur la procédure de suppression
     */
    public static function remove(OrganizationsHierarchy $organization) {
        $bdd = DB::getInstance();
        $report = array(
            "organizations" => 0,
            "users" => 0,
            "sessions" => 0,
            "errors" => 0,
            "logs" => array()
        );

        // On supprime d'abord les organisations enfants
        foreach ($organization->subOrganizations() as $subOrganization) {
            $subReport = self::remove($subOrganization);

            $report['organizations'] += $subReport['organizations'];
            $report['users'] += $subReport['users'];
            $report['sessions'] += $subReport['sessions'];
        }
        
        /* Suppression de l'organisation */

        // Suppression des utilisateurs
        foreach (UsersManager::getUsersByOrganization($organization, FALSE) as $user) {
            UsersManager::remove($user);
            $report['users']++;
        }
        
        // Suppression des sessions
        foreach (SessionsManager::getSessionsByOrganization($organization, FALSE) as $session) {
            SessionsManager::remove($session);
            $report['sessions']++;
        }
        
        // Suppression des logs
        foreach (LogsManager::getLogsIdByOrganization($organization) as $o) {
            LogsManager::remove($o);
        }

        try {
            $reponse = $bdd->prepare('DELETE FROM Organisations WHERE id = :id');
            $reponse->execute(array('id' => $organization->id()));

            $report['organizations']++;
        }
        catch (PDOException $e) {
            $report['errors']++;
            array_push($report['logs'], $e->getMessage());
        }

        return $report;
    }

    /**
	 * Indique si une organisation possède un parent
	 * 
	 * @param int $id
	 * @return bool
     */
    public static function isSubOrganization($id) {
        $bdd = DB::getInstance();
        
        $reponse = $bdd->prepare('SELECT parent FROM Organisations WHERE id = :id');
        $reponse->execute(array('id' => $id));
        $data = $response->fetch(PDO::FETCH_ASSOC);
        
        if(!$data)
            return "WRONG ID";

        return !is_null($data['parent']);
    }

    /**
	 * Indique si une organisation possède un parent
	 * 
	 * @param int $id
	 * @return mixed Retourne false en cas d'erreur
     */
    public static function haveSubOrganization($id) {
        if($id === NULL)
            return (self::getNbOrganization() > 0);
        
        $bdd = DB::getInstance();

        $reponse = $bdd->prepare('SELECT * FROM Organisations WHERE parent = :id');
        $reponse->execute(array('id' => $id));

        return $reponse->fetch();
    }

    /**
	 * Retourne le nombre d'organisation et de sous organisation (exclu l'organisation racine)
	 * 
	 * @return int Nombre d'organisations dans la base de données
     */
    public static function getNbOrganization() {
        $bdd = DB::getInstance();

        $reponse = $bdd->query('SELECT COUNT(*) AS nb FROM Organisations');

        return $reponse->fetch()['nb'];
    }

    /**
	 * Retourne un tableau d'OrganizationsHierarchy que l'utilisateur cible a l'autorisation d'accéder
	 * 
     * @param int $id_user
	 * @return Array Tableau d'OrganizationsHierarchy 
     */
    public static function getAvailableOrganizations($id_user = NULL) {
        $bdd = DB::getInstance();
        $organizations_array;

        if($id_user === NULL)
            $id_user = $_SESSION[APP_NAME]['idUser'];


        if($_SESSION[APP_NAME]['idOrganization'] === NULL) {	// Affiche TOUTES les organisations
            $organizations_array = self::getAllOrganizations();
        }
        else {	// Affiche les organisations auquel l'utilisateur a access
            $reponse = $bdd->prepare('SELECT * FROM Organisations WHERE id = :organisation AND parent IS NULL ORDER BY nom');
            $reponse->execute(array('organisation' => $_SESSION[APP_NAME]['idOrganization']));
            
            $data = $reponse->fetchAll(PDO::FETCH_ASSOC);

            $organizations_array = [];
            foreach ($data as $organization) {
                array_push($organizations_array, self::getOrganizationsHierarchy($organization['id']));
            }
        }        

        return $organizations_array;
    }

    /**
	 * Retourne toutes les organisations dans un tableau d'OrganizationsHierarchy
	 * 
	 * @return Array Tableau d'OrganizationsHierarchy 
     */
    public static function getAllOrganizations() {
        $bdd = DB::getInstance();
        $organizations = [];

        $response = $bdd->prepare('SELECT id, nom AS name, parent FROM Organisations WHERE parent IS NULL');
        $response->execute(array());

        $data = $response->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $d) {
            $hierarchy = new OrganizationsHierarchy($d);
            $hierarchy->setSubOrganizations( self::getOrganizationChilds($hierarchy->id()) );    // getOrganizationChilds() est une fonction recursive
            
            array_push($organizations, $hierarchy);
        }

        return $organizations;
    }

    /**
	 * Retourne un OrganizationsHierarchy
	 * 
     * @param int $id
	 * @return OrganizationsHierarchy
     */
    public static function getOrganizationsHierarchy($id) {
        $bdd = DB::getInstance();

        if(!self::idExists($id))
            return "WRONG ID";

        $response = $bdd->prepare('SELECT id, nom AS name, parent FROM Organisations WHERE id = :id');
        $response->execute(array('id' => $id));

        $data = $response->fetch(PDO::FETCH_ASSOC);

        $hierarchy = new OrganizationsHierarchy($data);
        $hierarchy->setSubOrganizations( self::getOrganizationChilds($hierarchy->id()) );    // getOrganizationChilds() est une fonction recursive
        
        return $hierarchy;
    }
    
    /**
     * Retourne un OrganizationsHierarchy a partir d'un nom
     * 
     * @param string $name Nom de l'organisation cible
     * @return OrganizationsHierarchy
     */ 
    public static function getOrganizationsHierarchyByName($name) {
        $bdd = DB::getInstance();
            
        if(!self::nameExists($name))
            return "WRONG NAME";

        $response = $bdd->prepare(
            self::$_req . '
            WHERE nom = :name');
        $response->execute(array('name' => $name));
        $id = $response->fetch(PDO::FETCH_ASSOC)['id'];
        
        return self::getOrganizationsHierarchy($id);
    }

    /**
     * Retourne les OrganizationsHierarchy enfants de l'organisation cible
     * 
     * @param int $id
     * @return Array Tableau d'OrganizationsHierarchy
     */ 
    public static function getOrganizationChilds($id) {
        $bdd = DB::getInstance();
        $organizations_array = [];

        $response = $bdd->prepare('SELECT id, nom AS name, parent FROM Organisations WHERE parent = :id');
        $response->execute(array('id' => $id));

        while($data = $response->fetch(PDO::FETCH_ASSOC)) {
            $organization = new OrganizationsHierarchy($data);
            $organization->setSubOrganizations( self::getOrganizationChilds($organization->id()) ); // Ajoute récursivement les sous-organisations
            array_push( $organizations_array, $organization );
        }

        return $organizations_array;
    }

    /**
     * Renvoie dans un tableau la liste des ids des organisations enfants (fonction récursive)
     * 
     * @param int $id
     * @return Array Tableau d'ids
     */ 
    public static function getChildsIds($id) {
        $bdd = DB::getInstance();
        $ids = [];

        $response = $bdd->prepare('SELECT id FROM Organisations WHERE parent = :id');
        $response->execute(array('id' => $id));

        $organizations = $response->fetchAll(PDO::FETCH_ASSOC);

        foreach ($organizations as $o) {
            array_push($ids, $o['id']);

            $childsIds = self::getChildsIds($o['id']);
            
            $ids = array_merge($ids, $childsIds);
        }

        return $ids;
    }

}

?>