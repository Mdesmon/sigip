<?php
/**
 * Objet représentant une session
 */
class Session implements JsonSerializable
{
    protected $_id;
    protected $_name;
    protected $_organization;
    protected $_author;
    protected $_dateStart;
    protected $_dateEnd;
    protected $_state;
    protected $_code;
    protected $_activeFormer;
    protected $_lastAction;
    protected $_version;

    
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
            'organization' => $this->organization(),
            'dateStart' => $this->dateStart(),
            'dateEnd' => $this->dateEnd(),
            'state' => $this->state(),
            'code' => $this->code()
        ];
    }
    
    public function id() { return $this->_id; }
    public function name() { return $this->_name; }
    public function organization() { return $this->_organization; }
    public function author() { return $this->_author; }
    public function dateStart() { return $this->_dateStart; }
    public function dateEnd() { return $this->_dateEnd; }
    public function state() { return $this->_state; }
    public function code() { return $this->_code; }
    public function activeFormer() { return $this->_activeFormer; }
    public function lastAction() { return $this->_lastAction; }
    public function version() { return $this->_version; }

    public function setId($id) { $this->_id = $id; }
    public function setName($name) { $this->_name = $name; }
    public function setOrganization($organization) { $this->_organization = $organization; }
    public function setAuthor($author) { $this->_author = $author; }
    public function setDateStart($dateStart) { $this->_dateStart = $dateStart; }
    public function setDateEnd($dateEnd) { $this->_dateEnd = $dateEnd; }
    public function setState($state) { $this->_state = $state; }
    public function setCode($code) { $this->_code = $code; }
    public function setActiveFormer($activeFormer) { $this->_activeFormer = $activeFormer; }
    public function setLastAction($lastAction) { $this->_lastAction = $lastAction; }
    public function setVersion($version) { $this->_version = $version; }
    
}

?>