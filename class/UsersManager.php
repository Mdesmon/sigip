<?php
/**
 * Gère les class User et DetailedUser
 */
class UsersManager
{
    private static $_req =
       'SELECT
            Utilisateurs.id,
            nom AS lastName,
            prenom AS name,
            nom_utilisateur AS username,
            email,
            type_utilisateur AS typeUser,
            organisation AS organization,
            dossier AS folder
        FROM Utilisateurs
        ';

    /**
     * Indique si un id d'utilisateur existe
     * 
     * @param int $id
     * @return bool
     */
    public static function idExists($id) {
        $bdd = DB::getInstance();
        
        $exists = FALSE;
        
        $reponse = $bdd->prepare('SELECT id FROM Utilisateurs WHERE id = :id_utilisateur');
        $reponse->execute(array('id_utilisateur' => $id));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
     * Indique si un username existe
     * 
     * @param string $username
     * @return bool
     */
    public static function usernameExists($username) {
        $bdd = DB::getInstance();
        
        $exists = FALSE;
        
        $reponse = $bdd->prepare('SELECT nom_utilisateur FROM Utilisateurs WHERE nom_utilisateur = :username');
        $reponse->execute(array('username' => $username));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
     * Retourne un objet User
     * 
     * @param int $id
     * @return User
     */ 
    public static function getUser($id) {
        $bdd = DB::getInstance();
            
        if(!self::idExists($id))
            return "WRONG ID";

        $response = $bdd->prepare(
            self::$_req . '
            WHERE Utilisateurs.id = :id');
        $response->execute(array('id' => $id));
        $data = $response->fetch(PDO::FETCH_ASSOC);
        
        return new User($data);
    }

    /**
     * Retourne un objet User depuis un username
     * 
     * @param string $username
     * @return User
     */ 
    public static function getUserByUsername($username) {
        $bdd = DB::getInstance();
        
        if(!self::usernameExists($username))
            return "WRONG USERNAME";

        $response = $bdd->prepare(
            self::$_req . '
            WHERE nom_utilisateur = :username');
        $response->execute(array('username' => $username));
        $data = $response->fetch(PDO::FETCH_ASSOC);

        return new User($data);
    }

    /**
     * Retourne dans un tableau les User contenues dans un Organization ou un OrganizationHierarchie
     * 
     * @param Organization $organization Organisation cible
     * @param Bool $recursive Récupère les User des sous organisations
     * @return array Un tableau de User
     */ 
    public static function getUsersByOrganization(Organization $organization, $recursive = TRUE) {
        $bdd = DB::getInstance();
        $users = [];

        $response = $bdd->prepare(
            self::$_req . '
            WHERE organisation = :organization');
        $response->execute(array('organization' => $organization->id()));
        $data = $response->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $d)
            array_push($users, new User($d));
        
        if($recursive)
            foreach ($organization->subOrganizations() as $subOrganization)
                $users = array_merge($users, self::getUsersByOrganization($subOrganization));
            
        return $users;
    }

    /**
     * Retourne la totalité des User de la base de données dans un tableau
     * 
     * @return array Tableau de User
     */ 
    public static function getAllUsers() {
        $bdd = DB::getInstance();
        $array = [];

        $response = $bdd->query(self::$_req);
        $users = $response->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $userData) {
            array_push($array, new User($userData));
        }
        
        return $array;
    }

    /**
     * Retourne le prénom et le nom de l'utilisateur cible dans une chaine
     * 
     * @param int $id
     * @return string Chaine contenant : "prénom nom"
     */ 
    public static function getNameAndLastName($id) {
        $bdd = DB::getInstance();
        
        if(self::idExists($id))
            return "WRONG ID";

        $reponse = $bdd->prepare(
           'SELECT nom AS lastName, prenom AS name
            FROM Utilisateurs
            WHERE id = :id');
        $reponse->execute(array('id' => $id));
        $data = $reponse->fetch(PDO::FETCH_ASSOC);
        
        return $data['name'] ." ". $data['lastName'];
    }

    /**
     * Supprime un utilisateur de la base de données
     * 
     * @param User $user
     * @return bool
     */ 
    public static function remove(User $user) {
        $bdd = DB::getInstance();
        $id = $user->id();
        
        $reponse = $bdd->prepare('DELETE FROM Responses WHERE user = :id');
        $reponse->execute(array('id' => $id));

        $reponse = $bdd->prepare('DELETE FROM Inscriptions WHERE utilisateur = :id');
        $reponse->execute(array('id' => $id));
        
        $reponse = $bdd->prepare('DELETE FROM Swap WHERE utilisateur = :id');
        $reponse->execute(array('id' => $id));
        
        $reponse = $bdd->prepare('DELETE FROM Logs WHERE utilisateur = :id');
        $reponse->execute(array('id' => $id));

        $reponse = $bdd->prepare('DELETE FROM Utilisateurs WHERE id = :id');
        $reponse->execute(array('id' => $id));

        // Suppression dossier utilisateur
        $userPath = "../content/users/". $user->folder();

        if($userPath !== "../content/users/")   // Evite la suppression accidentelle de tout le dossier users
            rrmdir($userPath);

        return true;
    }

    /**
     * Créer un utilisateur dans la base de données
     * 
     * @param User $user
     * @param string $password Mot de passe en claire
     * @param int $sendMail Indique si l'utilisateur doit recevoir son mail d'inscription
     * @return bool
     */ 
    public static function create(User $user, $password, $sendMail) {
        $bdd = DB::getInstance();
    
        /* Si aucun mot de passe définit, un mot de passe aléatoire à 8 caractères sera généré */
        if($password === "")
            $passwordHash = password_hash(generatePassword(), PASSWORD_DEFAULT);
        else
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        if(self::usernameExists( $user->username() ))
            return false;
        
        $folderName = generateUniqueFolderName($user->username());	// Genere un nom de dossier unique pour l'utilisateur
        
        $reponse = $bdd->prepare(
           'INSERT INTO Utilisateurs (nom, prenom, nom_utilisateur, mdp, email, organisation, type_utilisateur, dossier)
            VALUES (:nom, :prenom, :nom_utilisateur, :mdp, :email, :organization, :type_utilisateur, :dossier)');
        $executed = $reponse->execute(array(
            'nom' => $user->lastName(),
            'prenom' => $user->name(),
            'nom_utilisateur' => $user->username(),
            'mdp' => $passwordHash,
            'email' => $user->email(),
            'organization' => $user->organization(),
            'type_utilisateur' => $user->typeUser(),
            'dossier' => $folderName
        ));

        mkdir("../content/users/". $folderName);
        
        if($executed === false)
            return false;
        else if($sendMail AND $user->email() !== "")
            envoi_mail_inscription($user, $password);
    
        return true;
    }

    /**
     * Modifie un utilisateur
     * 
     * @param User $user
     * @param array $changes
     * @return void
     */
    public static function modify(User $user, $changes) {
        $bdd = DB::getInstance();
        
        if(property_exists($changes, 'name')) {
            $stmt = $bdd->prepare('UPDATE Utilisateurs SET prenom = :name WHERE id = :id');
            $stmt->execute(array(
                'id' => $user->id(),
                'name' => $changes->name
            ));
        }
        if(property_exists($changes, 'lastName')) {
            $stmt = $bdd->prepare('UPDATE Utilisateurs SET nom = :lastName WHERE id = :id');
            $stmt->execute(array(
                'id' => $user->id(),
                'lastName' => $changes->lastName
            ));
        }
        if(property_exists($changes, 'email')) {
            $stmt = $bdd->prepare('UPDATE Utilisateurs SET email = :email WHERE id = :id');
            $stmt->execute(array(
                'id' => $user->id(),
                'email' => $changes->email
            ));
        }
        if(property_exists($changes, 'organization')) {
            if($changes->organization === "")
                $changes->organization = null;

            $stmt = $bdd->prepare('UPDATE Utilisateurs SET organisation = :organization WHERE id = :id');
            $stmt->execute(array(
                'id' => $user->id(),
                'organization' => $changes->organization
            ));
        }
    }

    /**
     * Change le mot de passe d'un utilisateur et lui envoi éventuellement un mail pour le lui communiquer
     * 
     * @param User $user
     * @param string $password
     * @param bool $email
     * @return bool
     */
    public static function changePassword(User $user, $password, $sendMail) {
        $bdd = DB::getInstance();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $bdd->prepare('UPDATE Utilisateurs SET mdp = :hash WHERE id = :id');
        $stmt->execute(array(
            'id' => $user->id(),
            'hash' => $passwordHash
        ));

        if($sendMail AND $user->email() !== "")
            envoi_mail_resetPassword($user, $password);
        
        return true;
    }
    
}


?>