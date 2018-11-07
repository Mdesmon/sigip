<?php
/**
 * Gère les réponses des utilisateurs sur les sessions
 */
class ResponsesManager
{
    /**
	 * Intègre une réponse de type Posts dans la base de données
	 * 
	 * @param string $reponseApprenant
	 * @param int $page
	 * @param bool $anonymat
	 * @param Session $session
	 * @param User $user
	 * @return void
     */
    public static function envoyerReponsePosts($reponseApprenant, $page, $anonymat, Session $session, User $user) {
        $bdd = DB::getInstance();
        $anonymat = boolval($anonymat);

        // SWAP
        $strAnonymat = "";
        if($anonymat)
            $strAnonymat = 'anonymat=""';
        
        $xml = '<reponsePosts numero="'. $page .'" id_utilisateur="'. $user->id() .'" auteur="'. $user->name() ." ". $user->lastName() .'" '. $strAnonymat .'>'. $reponseApprenant .'</reponsePosts>';
        
        $reponse = $bdd->prepare(
           'INSERT INTO Swap (utilisateur, session, action)
            VALUES (:user, :session, :action)');
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'action' => $xml));
        
        // RESPONSE
        $data = array(
            'text' => $reponseApprenant,
            'anonymat' => $anonymat
        );

        $reponse = $bdd->prepare(
           'INSERT INTO Responses (user, session, page, data, send)
            VALUES (:user, :session, :page, :data, :send)');
        
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'page' => $page,
            'data' => json_encode($data),
            'send' => date("Y-m-d H:i:s")
        ));
    }

    /**
	 * Intègre une réponse de type Checkbox dans la base de données
	 * 
	 * @param string $reponseApprenant
	 * @param bool $correcte
	 * @param int $page
	 * @param Session $session
	 * @param User $user
	 * @return void
     */
    public static function envoyerReponseCheckbox($reponseApprenant, $correcte, $page, Session $session, User $user) {
        $bdd = DB::getInstance();
        $correcte = boolval($correcte);
        $strCorrecte = "";
        
        // SWAP
        if($correcte === TRUE)
            $strCorrecte = ' correcte=""';
        
        $xml = '<reponseCheckbox numero="'. $page .'" id_utilisateur="'. $user->id() .'"'. $strCorrecte .'>'. $reponseApprenant .'</reponseCheckbox>';
        
        $reponse = $bdd->prepare('INSERT INTO Swap (utilisateur, session, action)
                                    VALUES (:user, :session, :action)');
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'action' => $xml));
        
        // RESPONSE
        $data = array(
            'response' => $reponseApprenant,
            'correct' => $correcte
        );

        $reponse = $bdd->prepare(
           'INSERT INTO Responses (user, session, page, data, send)
            VALUES (:user, :session, :page, :data, :send)');
        
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'page' => $page,
            'data' => json_encode($data),
            'send' => date("Y-m-d H:i:s")
        ));
    }

    /**
	 * Intègre une réponse de type Radio dans la base de données
	 * 
	 * @param string $reponseApprenant
	 * @param bool $correcte
	 * @param int $page
	 * @param Session $session
	 * @param User $user
	 * @return void
     */
    public static function envoyerReponseRadio($reponseApprenant, $correcte, $page, Session $session, User $user) {
        $bdd = DB::getInstance();
        $correcte = boolval($correcte);
        $strCorrecte = "";
        
        // SWAP
        if($correcte === TRUE)
            $strCorrecte = ' correcte=""';
        
        $xml = '<reponseRadio numero="'. $page .'" id_utilisateur="'. $user->id() .'"'. $strCorrecte .'>'. $reponseApprenant .'</reponseRadio>';
        
        $reponse = $bdd->prepare('INSERT INTO Swap (utilisateur, session, action)
                                    VALUES (:user, :session, :action)');
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'action' => $xml));
        
        // RESPONSE
        $data = array(
            'response' => $reponseApprenant,
            'correct' => $correcte
        );

        $reponse = $bdd->prepare(
           'INSERT INTO Responses (user, session, page, data, send)
            VALUES (:user, :session, :page, :data, :send)');
        
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'page' => $page,
            'data' => json_encode($data),
            'send' => date("Y-m-d H:i:s")
        ));
    }

    /**
	 * Intègre une réponse de type ImageCliquable dans la base de données
	 * 
	 * @param float $x
	 * @param float $y
	 * @param int $page
	 * @param Session $session
	 * @param User $user
	 * @return void
     */
    public static function envoyerReponseImageCliquable($x, $y, $page, $session, $user) {
        $bdd = DB::getInstance();
        
        // SWAP
        $xml = '<reponseImageCliquable x="'. $x .'" y="'. $y .'" numero="'. $page .'" id_utilisateur="'. $user->id() .'" auteur="'. $user->name() ." ". $user->lastName() .'"></reponseImageCliquable>';
        
        $reponse = $bdd->prepare('INSERT INTO Swap (utilisateur, session, action)
                                    VALUES (:user, :session, :action)');
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'action' => $xml));
        
        // RESPONSE
        $data = array(
            'x' => $x,
            'y' => $y
        );

        $reponse = $bdd->prepare(
           'INSERT INTO Responses (user, session, page, data, send)
            VALUES (:user, :session, :page, :data, :send)');
        
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'page' => $page,
            'data' => json_encode($data),
            'send' => date("Y-m-d H:i:s")
        ));
    }

    /**
	 * Intègre une réponse de type Tableau dans la base de données
	 * 
	 * @param int $ligne
	 * @param int $colonne
	 * @param int $page
	 * @param Session $session
	 * @param User $user
	 * @return void
     */
    public static function envoyerReponseTableau($ligne, $colonne, $page, $session, $user) {
        $bdd = DB::getInstance();
        
        // SWAP
        $xml = '<reponseTableau ligne="'. $ligne .'" colonne="'. $colonne .'" numero="'. $page .'" id_utilisateur="'. $user->id() .'"></reponseTableau>';
        
        $reponse = $bdd->prepare('INSERT INTO Swap (utilisateur, session, action)
                                    VALUES (:user, :session, :action)');
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'action' => $xml));

        // RESPONSE
        $data = array(
            'row' => $ligne,
            'col' => $colonne
        );

        $reponse = $bdd->prepare(
           'INSERT INTO Responses (user, session, page, data, send)
            VALUES (:user, :session, :page, :data, :send)');
        
        $reponse->execute(array(
            'user' => $user->id(),
            'session' => $session->id(),
            'page' => $page,
            'data' => json_encode($data),
            'send' => date("Y-m-d H:i:s")
        ));
    }

    /**
	 * Retourne les réponses correspondant a la session cible
	 * 
     * @param Session $session
	 * @param int $nbPages
	 * @param $user
	 * @return array
     */
    public static function getSessionsResponses(Session $session, $nbPages, $user = NULL) {
        $bdd = DB::getInstance();
        $data = array('pages' => []);

        for ($i=0; $i < $nbPages ; $i++) { 
            array_push($data['pages'], ResponsesManager::getPageResponses($session, $i, $user));
        }

        return $data;
    }

    /**
	 * Retourne les réponses de la page cible d'une session
	 * 
     * @param Session $session
	 * @param int $page
	 * @param $user
	 * @return array
     */
    public static function getPageResponses(Session $session, $page, $user = NULL) {
        $bdd = DB::getInstance();

        $req =
           'SELECT user, data
            FROM Responses
            WHERE
                session = :session
                AND page = :page';
        $args = array(
            'session' => $session->id(),
            'page' => $page
        );

        if($user) {
            $req .= ' AND user = :user';
            $args['user'] = $user->id();
        }

        $reponse = $bdd->prepare($req);
        $reponse->execute($args);

        return $reponse->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
	 * Copie les réponses d'une session vers une autre session
	 * 
     * @param Session $source
     * @param Session $dest
	 * @return void
     */
    public static function copy(Session $source, Session $dest) {
        $bdd = DB::getInstance();

        $reponse = $bdd->prepare(
           'SELECT user, page, data, send
            FROM Responses
            WHERE
                session = :session');
        $reponse->execute(array(
            'session' => $source->id()
        ));

        $data = $reponse->fetchAll(PDO::FETCH_ASSOC);

        $req = $bdd->prepare('INSERT INTO Responses (user, session, page, data, send) VALUES(:user, '. $dest->id() .', :page, :data, :send)');
        
        foreach ($data as $d) {
            $req->execute(array(
                'user' => $d['user'],
                'page' => $d['page'],
                'data' => $d['data'],
                'send' => $d['send']
            ));
        }
    }

    /**
	 * Supprime de la base de données toutes les réponses correspondant a la session cible
	 * 
     * @param Session $session
	 * @return void
     */
    public static function removeBySession(Session $session) {
        $bdd = DB::getInstance();
        
        $reponse = $bdd->prepare('DELETE FROM Responses WHERE session = :id');
        $reponse->execute(array('id' => $session->id()));
    }

    /**
	 * Supprime de la base de données toutes les réponses correspondant a l'utilisateur cible
	 * 
     * @param User $user
	 * @return void
     */
    public static function removeByUser(User $user) {
        $bdd = DB::getInstance();
        
        $reponse = $bdd->prepare('DELETE FROM Responses WHERE utilisateur = :id');
        $reponse->execute(array('id' => $user->id()));
    }

}

?>