<?php	
	/**
     * retourne une balise img contenant l'image cible encodé en base64
     * 
     * @param string $name
     * @return string
     */ 
	function addImgBase64($name) {
		return '<img src="' . file_get_contents("../img/base64/" . $name . ".b64") . '" />';
	}

	/**
     * Retourne les balises <option> contenant les organisations accéssible par l'utilisateur
     * 
     * @param bool $tout
     * @return string
     */ 
	function options_organization($tout = FALSE) {
		// Generation des <option>
		if($tout) {
			if($_SESSION[APP_NAME]['idOrganization'] === NULL)
				echo '<option selected value="-1">TOUT</option>';
			else if(Controls::control_user([SUPERADMIN]))
				echo '<option value="-1">TOUT</option>';
		}
		
		$organisations = OrganizationsManager::getAvailableOrganizations();

		foreach($organisations as $organisation) {
			if($organisation->id() == $_SESSION[APP_NAME]['activeOrganization'])
				echo '<option selected value="'. $organisation->id() .'">'. $organisation->name() .'</option>';
			else
				echo '<option value="'. $organisation->id() .'">'. $organisation->name() .'</option>';
			
			// Generation des sous-organisations
			foreach($organisation->subOrganizations() as $subOrganization) {
				if($subOrganization->id() == $_SESSION[APP_NAME]['activeOrganization'])
					echo '<option selected value="'. $subOrganization->id() .'">- '. $subOrganization->name() .'</option>';
				else
					echo '<option value="'. $subOrganization->id() .'">- '. $subOrganization->name() .'</option>';
			}
		}
	}

	/**
     * Convertie un id en string compréhensible par l'utilisateur
     * 
     * @param int $id
     * @return string
     */ 
	function getTypeUser($id) {
		if($id == APPRENANT)
			return "Apprenant";
		elseif($id == FORMATEUR)
			return "Formateur";
		elseif($id == SUPERVISEUR)
			return "Superviseur";
		elseif($id == ADMINISTRATEUR)
			return "Administrateur";
		elseif($id == SUPERADMIN)
			return "Superadmin";
	}

	/**
	 * Retourne une représentation JSON indexé selon $key.
	 * Les instances envoyés en paramètre doivent implémenter la fonction jsonSerialize pour fonctionner
	 * 
	 * @param array $array_obj Tableau d'instances
	 * @param string $key Clé qui servira d'index
	 * @return string Représentation JSON
     */
	function serializeByKey($array_obj, $key = "id") {
		$json = "{";
		
		foreach ($array_obj as $o)
			$json .= $o->$key() . ':' . json_encode($o) . ',';
		
		$json = rtrim($json, ',');
		$json .= "}";

		return $json;
	}
	
?>
