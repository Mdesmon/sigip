<?php
/**
 * Objet représentant une Hierarchie d'organisations
 * Un objet organizationsHierarchy est un objet Organization contenant ses sous organisations
 */
class OrganizationsHierarchy extends Organization implements JsonSerializable {
    public $_subOrganizations;

    public function subOrganizations() { return $this->_subOrganizations; }

    public function setSubOrganizations($subOrganizations) { $this->_subOrganizations = $subOrganizations; }

    public function jsonSerialize() {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'subOrganizations' => $this->subOrganizations()
        ];
    }
}

?>