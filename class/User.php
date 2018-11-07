<?php
/**
 * Objet représentant un utilisateur
 */
class User implements JsonSerializable
{
    protected $_id;
    protected $_name;
    protected $_lastName;
    protected $_username;
    protected $_email;
    protected $_typeUser;
    protected $_organization;
    protected $_folder;
    
    
    public function __construct($data) {
        $this->hydrate($data);
    }

    public function hydrate(array $data) {
        foreach($data as $key => $value) {
            $method = "set".ucfirst($key);

            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'lastName' => $this->lastName(),
            'username' => $this->username(),
            'email' => $this->email(),
            'typeUser' => $this->typeUser(),
            'organization' => $this->organization()
        ];
    }
    
    public function id() { return $this->_id; }
    public function name() { return $this->_name; }
    public function lastName() { return $this->_lastName; }
    public function username() { return $this->_username; }
    public function email() { return $this->_email; }
    public function typeUser() { return $this->_typeUser; }
    public function organization() { return $this->_organization; }
    public function folder() { return $this->_folder; }

    public function setId($id) { $this->_id = $id; }
    public function setName($name) { $this->_name = $name; }
    public function setLastName($lastName) { $this->_lastName = $lastName; }
    public function setUsername($username) { $this->_username = $username; }
    public function setEmail($email) { $this->_email = $email; }
    public function setTypeUser($typeUser) { $this->_typeUser = $typeUser; }
    public function setOrganization($organization) { $this->_organization = $organization; }
    public function setFolder($folder) { $this->_folder = $folder; }
    
}

?>