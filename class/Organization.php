<?php
/**
 * Objet représentant une organisation
 */
class Organization
{
    protected $_id;
    protected $_name;
    protected $_parent;
    
    
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
    
    public function id() { return $this->_id; }
    public function name() { return $this->_name; }
    public function parent() { return $this->_parent; }

    public function setId($id) { $this->_id = $id; }
    public function setName($name) { $this->_name = $name; }
    public function setParent($parent) { $this->_parent = $parent; }
    
}

?>