<?php
/**
 * Gère la class Etablissement
 */
class EtablissementsManager
{
    private static $_req =
       'SELECT *
        FROM Etablissements
       ';

    /**
     * Indique si un id d'établissement existe
     * 
     * @param int $id
     * @return bool
     */
    public static function idExists($id) {
        $bdd = DB::getInstance();
        
        $exists = FALSE;
        
        $reponse = $bdd->prepare('SELECT id FROM Etablissements WHERE id = :id_etablissement');
        $reponse->execute(array('id_etablissement' => $id));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
     * Indique si un rne existe
     * 
     * @param string $rne
     * @return bool
     */
    public static function rneExists($rne) {
        $bdd = DB::getInstance();
        
        $exists = FALSE;
        
        $reponse = $bdd->prepare('SELECT rne FROM Etablissements WHERE rne = :rne');
        $reponse->execute(array('rne' => $rne));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
     * Retourne un objet Etablissement
     * 
     * @param int $id
     * @return Etablissement
     */ 
    public static function getEtablissement($id) {
        $bdd = DB::getInstance();
            
        if(!self::idExists($id))
            return "WRONG ID";

        $response = $bdd->prepare(
            self::$_req . '
            WHERE Etablissements.id = :id');
        $response->execute(array('id' => $id));
        $data = $response->fetch(PDO::FETCH_ASSOC);
        
        return new Etablissement($data);
    }

    /**
     * Retourne la totalité des Etablissments de la base de données dans un tableau
     * 
     * @return array Tableau de Etablissment
     */ 
    public static function getAllEtablissements() {
        $bdd = DB::getInstance();
        $array = [];

        $response = $bdd->query(self::$_req);
        $etablissements = $response->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($etablissements as $e) {
            array_push($array, new Etablissement($e));
        }
        
        return $array;
    }

    /**
     * Supprime un établissement de la base de données
     * 
     * @param Etablissement $etablissement
     * @return bool
     */ 
    public static function remove(Etablissement $e) {
        $bdd = DB::getInstance();
        $id = $e->id();

        $reponse = $bdd->prepare('DELETE FROM Etablissements WHERE id = :id');
        return $reponse->execute(array('id' => $id));
    }

    /**
     * Créer un établissement dans la base de données
     * 
     * @param Etablissement $etablissement
     * @return bool
     */ 
    public static function create(Etablissement $e) {
        $bdd = DB::getInstance();
        
        $reponse = $bdd->prepare(
           'INSERT INTO Etablissements (name, rne)
            VALUES (:name, :rne)');
        $executed = $reponse->execute(array(
            'name' => $e->name(),
            'rne' => $e->rne()
        ));
    
        return $executed;
    }

    /**
     * Modifie un établissement
     * 
     * @param Etablissement $etablissement
     * @param array $changes
     * @return void
     */
    public static function modify(Etablissement $e, $changes) {
        $bdd = DB::getInstance();
        
        if(property_exists($changes, 'name')) {
            $stmt = $bdd->prepare('UPDATE Etablissements SET nom = :name WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'name' => $changes->name
            ));
        }
        if(property_exists($changes, 'rne')) {
            $stmt = $bdd->prepare('UPDATE Etablissements SET rne = :rne WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'rne' => $changes->rne
            ));
        }
    }
}
