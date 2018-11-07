<?php
	include "../includes/global_include.php";

	$pathFile = tempnam(sys_get_temp_dir(), 'data');
	$file = fopen($pathFile, 'w+');

    $contents = file_get_contents($_FILES['file']['tmp_name'][0]);

	// TRAITEMENT

	fputs($file, $contents);

	// FIN TRAITEMENT

	fclose($file);
	
	header("Content-type: text" );
	header("Content-Disposition: attachment; filename=textannu.out" );
	header('Content-Length: '. filesize($pathFile));

	readfile($pathFile);
	unlink($pathFile);
?>