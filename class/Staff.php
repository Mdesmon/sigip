<?php
/**
 * Objet représentant le personnel inscrit dans la table Textannu
 */
class Staff implements JsonSerializable
{
    protected $_id;
    protected $_numen;
    protected $_sn;
    protected $_nompatro;
    protected $_givenname;
    protected $_datenaissance;
    protected $_codecivilite;
    protected $_title;
    protected $_rne;
    protected $_rneextract;
    protected $_finfonction;
    protected $_dateff;
    protected $_fredurne;
    protected $_oldemployeenumber;

    
    public function __construct($data) {
        $this->hydrate($data);

        $this->initNompatro();
        $this->initRneextract();
        $this->initFreedurne();
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
            'numen' => $this->numen(),
            'sn' => $this->sn(),
            'givenname' => $this->givenname(),
            'datenaissance' => $this->datenaissance(),
            'codecivilite' => $this->codecivilite(),
            'title' => $this->title(),
            'rne' => $this->rne(),
            'rneextract' => $this->rneextract(),
            'finfonction' => $this->finfonction(),
            'dateff' => $this->dateff(),
            'fredurne' => $this->fredurne(),
            'oldemployeenumber' => $this->oldemployeenumber()
        ];
    }
    
    public function id() { return $this->_id; }
    public function numen() { return $this->_numen; }
    public function sn() { return $this->_sn; }
    public function nompatro() { return $this->_nompatro; }
    public function givenname() { return $this->_givenname; }
    public function datenaissance() { return $this->_datenaissance; }
    public function codecivilite() { return $this->_codecivilite; }
    public function title() { return $this->_title; }
    public function rne() { return $this->_rne; }
    public function rneextract() { return $this->_rneextract; }
    public function finfonction() { return $this->_finfonction; }
    public function dateff() { return $this->_dateff; }
    public function fredurne() { return $this->_fredurne; }
    public function oldemployeenumber() { return $this->_oldemployeenumber; }
    
    public function setId($id) { $this->_id = $id; }
    public function setNumen($numen) { $this->_numen = $numen; }
    public function setSn($sn) { $this->_sn = ucfirst(strtolower($sn)); }
    public function setNompatro($nompatro) { $this->_nompatro = ucfirst(strtolower($nompatro)); }
    public function setGivenname($givenname) { $this->_givenname = ucfirst(strtolower($givenname)); }
    public function setDatenaissance($datenaissance) { $this->_datenaissance = $datenaissance; }
    public function setCodecivilite($codecivilite) { $this->_codecivilite = $codecivilite; }
    public function setTitle($title) { $this->_title = $title; }
    public function setRne($rne) { $this->_rne = $rne; }
    public function setRneextract($rneextract) { $this->_rneextract = $rneextract; }
    public function setFinfonction($finfonction) { $this->_finfonction = $finfonction; }
    public function setDateff($dateff) { $this->_dateff = $dateff; }
    public function setOldemployeenumber($oldemployeenumber) { $this->_oldemployeenumber = $oldemployeenumber; }

    public function initNompatro() {
        if($this->nompatro() === "") {
            $this->setNompatro($this->sn());
        }
    }

    public function initRneextract() {
        if($this->rneextract() === null) {
            $this->setRneextract($this->rne());
        }
    }

    public function initFreedurne() {
        $fredurne = sprintf('%s$UAJ$PU$%s$%sST0$GIP$000',
            $this->rne(),
            $this->title(),
            $this->rne()
        );
        $this->_fredurne = $fredurne;
    }

}
