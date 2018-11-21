<?php
/**
 * Objet représentant un établissement
 */
class Etablissement implements JsonSerializable
{
    protected $_id;
    protected $_name;
    protected $_rne;

    
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
            'rne' => $this->lastName()
        ];
    }
    
    public function id() { return $this->_id; }
    public function name() { return $this->_name; }
    public function rne() { return $this->_rne; }

    public function setId($id) { $this->_id = $id; }
    public function setName($name) { $this->_name = $name; }
    public function setRne($rne) { $this->_rne = $rne; }
    
}
