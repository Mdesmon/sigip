<?php
/**
 * Objet représentant un utilisateur, toutes les données existantes sur l'utilisateurs sont disponibles
 */
class DetailedUser extends User
{
    private $_passwordHash;

    
    public function __construct($data) {
        hydrate($data);
    }

    public function hydrate(array $data) {
        foreach($data as $key => $value) {
            $method = "set".ucfirst($key);

            if(method_exists($this, $method)) {
                $this.$method($value);
            }
        }
    }
    

    public function passwordHash() { return $this->passwordHash; }

    public function setPassword_hash($passwordHash) { $this->passwordHash = $passwordHash; }

}

?>