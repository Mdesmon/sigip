<?php
    include "../includes/global_include.php";

    session_start();

    /* SECURITE */
	if (
		!Controls::isConnected()
		OR Controls::control_user([APPRENANT])
		OR (
			!Controls::inPrimaryOrganization()
			AND !Controls::organization_access(Controls::getActiveOrganization(), Controls::getConnectedUser())
		)
	)
	{
		header('Location: ../index.php');
		exit();
	}

    $bdd = DB::getInstance();

	$pathFile = tempnam(sys_get_temp_dir(), 'data');
	$file = fopen($pathFile, 'w+');
    $delimiter = ";";

	// CREATION DU CSV

    $reponse = $bdd->prepare(
       'SELECT prenom, nom, nom_utilisateur, email, Types_utilisateurs.type
        FROM Utilisateurs
        INNER JOIN types_utilisateurs
            ON Utilisateurs.type_utilisateur = Types_utilisateurs.id
        WHERE 1
    ');
    $reponse->execute(array());
    $data = $reponse->fetchAll(PDO::FETCH_NUM);

    foreach($data as $d) {
        fputcsv($file, $d, $delimiter);
    }

	// FIN CSV

	fclose($file);
	
	header("Content-type: text/csv" );
	header("Content-Disposition: attachment; filename=utilisateurs_". date("d-m-Y") .".csv");
	header('Content-Length: '. filesize($pathFile));

	readfile($pathFile);
	unlink($pathFile);
?>