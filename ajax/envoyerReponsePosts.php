<?php
	session_start();
	include '../includes/global_include.php';
	
	$anonymat = ($_POST['anonymat'] === "true");

	// SECURITE
	if(!Controls::isConnected()) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Envoyer réponse posts) par un utilisateur non connecté.", INCIDENT);
		return FALSE;
	}

	$session = SessionsManager::getSession($_POST['id_session']);
	$user = UsersManager::getUser($_SESSION[APP_NAME]['idUser']);

	if($session === FALSE) {
		echo "WRONG SESSION ID";
		exit();
	}
	
	if(!Controls::session_access($session, $user)) {
		echo "ACTION NON AUTORISE";
		LogsManager::addLog("Action non autorisée (Envoyer réponse posts) par ". $user->username() .".", INCIDENT);
		return FALSE;
	}

	// ENVOYER REPONSE
	ResponsesManager::envoyerReponsePosts(escapeChars($_POST['reponse']), $_POST['ecran'], $anonymat, $session, $user);

	function escapeChars($str) {
		$search = array('<', '>');
		$replace = array('&lt;', '&gt;');
		
		$str = str_replace($search, $replace, $str);

		return $str;
	}
?>