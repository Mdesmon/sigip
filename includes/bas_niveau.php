<?php

function generatePassword($len = 8) {
	if($len > 53) {
		$len = 53;
	}
	
	return substr(password_hash(uniqid(), PASSWORD_BCRYPT), 7, $len);
}

function generateUniqueFolderName($prefix = "") {
	$folderName = formatString($prefix) . str_replace(' ','',microtime());
	
	return $folderName;
}

function getExtension($str) {
	return strtolower( substr( strrchr($str, '.') ,1) );
}

function renamePhp2Html($str) {
	if(getExtension($str) == 'php') {
		$str = substr( $str, 0, strrpos($str, '.') ) . '.html';
	}
	
	return $str;
}

function rrmdir($path) {	// Supprime un dossier meme s'il n'est pas vide
	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? rrmdir($file) : unlink($file);
	}
	rmdir($path);
	return;
}

function jsString($str, $doubleQuotes = FALSE) {
	$str = str_replace(array("\n", "\r"), "", $str);
	$str = addslashes($str);
	
	if($doubleQuotes)
		return '"'. $str .'"';
	else
		return "'". $str ."'";
}

//This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
function convertPHPSizeToBytes($sSize)  
{  
    if ( is_numeric( $sSize) ) {
       return $sSize;
    }
    $sSuffix = substr($sSize, -1);  
    $iValue = substr($sSize, 0, -1);  
    switch(strtoupper($sSuffix)){  
    case 'P':
        $iValue *= 1024;
    case 'T':
        $iValue *= 1024;
    case 'G':
        $iValue *= 1024;
    case 'M':
        $iValue *= 1024;
    case 'K':
        $iValue *= 1024;
        break;
    }  
    return $iValue;
}

function getMaximumFileUploadSize()
{
    return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
}

function getMaximumPostSize() {
	return convertPHPSizeToBytes(ini_get('post_max_size'));
}

function formatDate($str, $reverse = FALSE, $separateur = "-", $forcerFormat = FALSE) {	// Transforme une date Francaise en date MySQL (forcer en format 10 pour un DATE valide mySQL)
	if(strlen($str) != 8 AND strlen($str) != 10)
		return FALSE;
	
	if($reverse === FALSE) {
		if(strlen($str) == 8) {
			if($forcerFormat == 10) {
				$resultat = '20' . substr($str, 6) . $separateur . substr($str, 3, 2) . $separateur . substr($str, 0, 2);
			} else {
				$resultat = substr($str, 6) . $separateur . substr($str, 3, 2) . $separateur . substr($str, 0, 2);
			}
		}
		else if(strlen($str) == 10) {
			if($forcerFormat == 8) {
				$resultat = substr($str, 8) . $separateur . substr($str, 3, 2) . $separateur . substr($str, 0, 2);
			} else {
				$resultat = substr($str, 6) . $separateur . substr($str, 3, 2) . $separateur . substr($str, 0, 2);
			}
		}
	}
	else {	// $reverse = TRUE
		if(strlen($str) == 8) {
			if($forcerFormat == 10) {
				$resultat = substr($str, 6) . $separateur . substr($str, 3, 2) . $separateur . '20' . substr($str, 0, 2);
			} else {
				$resultat = substr($str, 6) . $separateur . substr($str, 3, 2) . $separateur . substr($str, 0, 2);
			}
		} else if(strlen($str) == 10) {
			if($forcerFormat == 8) {
				$resultat = substr($str, 8) . $separateur . substr($str, 5, 2) . $separateur . substr($str, 2, 2);
			} else {
				$resultat = substr($str, 8) . $separateur . substr($str, 5, 2) . $separateur . substr($str, 0, 4);
			}
		}
	}
	
	return $resultat;
}

// Renvoie l'encodage d'un fichier, si le fichier n'est pas un texte, renvoi "binary"
function getEncoding($filename) {
	$info = finfo_open(FILEINFO_MIME_ENCODING);
    $type = finfo_buffer($info, file_get_contents($filename));
    finfo_close($info);

    return $type;
}

function isUTF8($filename) {
    $type = getEncoding($filename);
    return ($type == 'utf-8' || $type == 'us-ascii');	// UTF8 ou compatible UTF8
}

function toUTF8($url) {
    if(!file_exists($url))
		return false; // Exit la fonction si le fichier n'existe pas
    if(isUTF8($url))
		return false; // Exit la fonction si c'est déjà UTF-8

	$encode_in = strtoupper(getEncoding($url));
	$contents = file_get_contents($url);
    $file = fopen($url, 'w+');
	fputs($file, iconv($encode_in, "UTF-8//TRANSLIT", $contents));
    fclose($file);
    return true;
}

function space2underscore($t) {
	return str_replace(' ', '_', $t);
}

function underscore2space($t) {
	return str_replace('_', ' ', $t);
}

// Renvoi une chaine de caractère utilisable en tant que nom de fichier
function formatString($texte) {
	$texte = str_replace(
		array(
			'à', 'â', 'ä', 'á', 'ã', 'å',
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
			'Ç', 'Ÿ', 'Ñ',
			' ', '*', '!', '/', '\\', ':', '"', "|", "<", ">"
		),
		array(
			'a', 'a', 'a', 'a', 'a', 'a', 
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
			'C', 'Y', 'N',
			'_', '_', '_', '_', '_', '_', '_', '_', '_', '_'
		),$texte);
	return $texte;
}

function dateInterval2Seconds($interval) {
	$j = intval($interval->format('%a'));
	$h = intval($interval->format('%h'));
	$m = intval($interval->format('%m'));
	$s = intval($interval->format('%s'));
	
	$secondes_total = $s + ($m*60) + ($h*3600) + (j*86400);
	
	return secondes_total;
}

class DateIntervalEnhanced extends DateInterval {

    public function recalculate()
    {
        $from = new DateTime;
        $to = clone $from;
        $to = $to->add($this);
        $diff = $from->diff($to);
        foreach ($diff as $k => $v) $this->$k = $v;
        return $this;
    }

}

?>