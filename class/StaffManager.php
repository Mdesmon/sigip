<?php
/**
 * Gère la class Staff
 */
class StaffManager
{
    private static $_req =
       'SELECT
            id, numen, sn, nompatro, givenname, datenaissance,
            codevicilite, title, rne, rneextract, finfonction,
            dateff, fredurne, oldemployeenumber
        FROM Textannu
       ';

    /**
     * Indique si un id existe
     * 
     * @param int $id
     * @return bool
     */
    public static function idExists($id) {
        $bdd = DB::getInstance();
        
        $exists = FALSE;
        
        $reponse = $bdd->prepare('SELECT id FROM Textannu WHERE id = :id');
        $reponse->execute(array('id' => $id));
        
        if($reponse->fetch())
            $exists = TRUE;
        
        return $exists;
    }

    /**
     * Retourne un objet Etablissement
     * 
     * @param int $id
     * @return Staff
     */ 
    public static function getStaff($id) {
        $bdd = DB::getInstance();
            
        if(!self::idExists($id))
            return "WRONG ID";

        $response = $bdd->prepare(
            self::$_req . '
            WHERE Staff.id = :id');
        $response->execute(array('id' => $id));
        $data = $response->fetch(PDO::FETCH_ASSOC);
        
        return new Staff($data);
    }

    /**
     * Retourne la totalité des Staff de la base de données dans un tableau
     * 
     * @return array Tableau de Staff
     */ 
    public static function getAllStaff() {
        $bdd = DB::getInstance();
        $array = [];

        $response = $bdd->query(self::$_req);
        $staff = $response->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($staff as $e) {
            array_push($array, new Staff($e));
        }
        
        return $array;
    }

    /**
     * Supprime un Staff de la base de données
     * 
     * @param Staff $staff
     * @return bool
     */ 
    public static function remove(Staff $e) {
        $bdd = DB::getInstance();
        $id = $e->id();

        $reponse = $bdd->prepare('DELETE FROM Staff WHERE id = :id');
        return $reponse->execute(array('id' => $id));
    }

    /**
     * Créer un Staff dans la base de données
     * 
     * @param Staff $staff
     * @return bool
     */ 
    public static function create(Staff $e) {
        $bdd = DB::getInstance();
        
        if(!EtablissementsManager::rneExists($e->rne()))
            return 'WRONG RNE';

        $reponse = $bdd->prepare(
           'INSERT INTO Textannu (
               numen, sn, nompatro, givenname, datenaissance,
               codecivilite, title, rne, rneextract, finfonction,
               dateff, fredurne, fredufonctadm, freduotp, oldemployeenumber
            )
            VALUES (
                :numen, :sn, :nompatro, :givenname, :datenaissance,
                :codecivilite, :title, :rne, :rneextract, :finfonction,
                :dateff, :fredurne, :fredufonctadm, :freduotp, :oldemployeenumber
            )'
        );
        $executed = $reponse->execute(array(
            'numen' => $e->numen(),
            'sn' => $e->sn(),
            'nompatro' => $e->nompatro(),
            'givenname' => $e->givenname(),
            'datenaissance' => $e->datenaissance(),
            'codecivilite' => $e->codecivilite(),
            'title' => $e->title(),
            'rne' => $e->rne(),
            'rneextract' => $e->rneextract(),
            'finfonction' => $e->finfonction(),
            'dateff' => $e->dateff(),
            'fredurne' => $e->fredurne(),
            'fredufonctadm' => $e->title(),
            'freduotp' => $e->rne(),
            'oldemployeenumber' => $e->oldemployeenumber()
        ));
    
        return $executed;
    }

    /**
     * Modifie un établissement
     * 
     * @param Staff $staff
     * @param array $changes
     * @return void
     */
    public static function modify(Staff $e, $changes) {
        $bdd = DB::getInstance();
        
        if(property_exists($changes, 'givenname')) {
            $stmt = $bdd->prepare('UPDATE Staff SET givenname = :givenname WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'givenname' => $changes->givenname
            ));
        }
        if(property_exists($changes, 'rne')) {
            $stmt = $bdd->prepare('UPDATE Staff SET rne = :rne WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'rne' => $changes->rne
            ));
        }
    }
}
