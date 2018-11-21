<?php
	include "../includes/global_include.php";

	header("Content-type: text" );
	header("Content-Disposition: attachment; filename=textannu.out" );
	
	$pathFile = tempnam(sys_get_temp_dir(), 'data');
	$file = fopen($pathFile, 'w+');

	toUTF8($_FILES['file']['tmp_name']);
    $contents = file_get_contents($_FILES['file']['tmp_name']);
			
	fputs($file, formatFile($contents));
	fclose($file);
	
	header('Content-Length: '. filesize($pathFile));
	readfile($pathFile);
	unlink($pathFile);


	function formatFile($texte) {
		$texte = str_replace(
			array(
				';', 'à', 'â', 'ä', 'á', 'ã', 'å',
				'î', 'ï', 'ì', 'í', 
				'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
				'ù', 'û', 'ü', 'ú', 
				'é', 'è', 'ê', 'ë', 
				'ç', 'ÿ', 'ñ',
				'À', 'Â', 'Ä', 'Á', 'Ã', 'Å',
				'Î', 'Ï', 'Ì', 'Í', 
				'Ô', 'Ö', 'Ò', 'Ó', 'Õ', 'Ø', 
				'Ù', 'Û', 'Ü', 'Ú', 
				'É', 'È', 'Ê', 'Ë', 
				'Ç', 'Ÿ', 'Ñ', "'", ' '
			),
			array(
                '|', 'a', 'a', 'a', 'a', 'a', 'a', 
				'i', 'i', 'i', 'i', 
				'o', 'o', 'o', 'o', 'o', 'o', 
				'u', 'u', 'u', 'u', 
				'e', 'e', 'e', 'e', 
				'c', 'y', 'n', 
				'A', 'A', 'A', 'A', 'A', 'A', 
				'I', 'I', 'I', 'I', 
				'O', 'O', 'O', 'O', 'O', 'O', 
				'U', 'U', 'U', 'U', 
				'E', 'E', 'E', 'E', 
				'C', 'Y', 'N', '', ''
			),$texte);
		return $texte;
	}

?>