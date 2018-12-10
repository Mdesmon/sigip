<?php
/**
 * Gère la class Staff
 */
class StaffManager
{
    private static $_req =
       'SELECT
            id, numen, sn, nompatro, givenname, DATE_FORMAT(datenaissance, "%d/%m/%Y") AS datenaissance,
            codecivilite, title, rne, rneextract, finfonction,
            DATE_FORMAT(dateff, "%d/%m/%Y") AS dateff, fredurne, oldemployeenumber
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
            WHERE Textannu.id = :id');
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

        $reponse = $bdd->prepare('DELETE FROM Textannu WHERE id = :id');
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
        
        if(property_exists($changes, 'numen')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET numen = :numen WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'numen' => $changes->numen
            ));
        }
        if(property_exists($changes, 'sn')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET sn = :sn WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'sn' => $changes->sn
            ));
        }
        if(property_exists($changes, 'nompatro')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET nompatro = :nompatro WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'nompatro' => $changes->nompatro
            ));
        }
        if(property_exists($changes, 'givenname')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET givenname = :givenname WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'givenname' => $changes->givenname
            ));
        }
        if(property_exists($changes, 'rne')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET rne = :rne WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'rne' => $changes->rne
            ));
        }
        if(property_exists($changes, 'title')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET title = :title WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'title' => $changes->title
            ));
        }
        if(property_exists($changes, 'datenaissance')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET datenaissance = :datenaissance WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'datenaissance' => $changes->datenaissance
            ));
        }
        if(property_exists($changes, 'codecivilite')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET codecivilite = :codecivilite WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'codecivilite' => $changes->codecivilite
            ));
        }
        if(property_exists($changes, 'dateff')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET dateff = :dateff WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'dateff' => $changes->dateff
            ));
        }
        if(property_exists($changes, 'finfonction')) {
            $stmt = $bdd->prepare('UPDATE Textannu SET finfonction = :finfonction WHERE id = :id');
            $stmt->execute(array(
                'id' => $e->id(),
                'finfonction' => $changes->finfonction
            ));
        }
    }
}
