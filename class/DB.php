<?php
/**
 * Permet de gérer les accès à la base de données
 */
class DB {
	private static $_instance;

	/**
	 * Retourne un objet PDO
	 * 
	 * @return PDO
	 */
	public static function getInstance() {
		if(!isset($_instance)) {
			try {
				$_instance = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
				$_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			}
			catch (PDOException $e) {
				return $e->getMessage();
			}
		}
		
		return $_instance;
	}
}

?>