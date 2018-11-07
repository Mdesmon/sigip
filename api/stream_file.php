<?php

// Avoid sending unexpected errors to the client - we should be serving a file,
// we don't want to corrupt the data we send
ini_set('display_errors', '0');

$path = '../content/video.mp4';
$chunkSize = 1024*8;

if (!file_exists($path)) {
	header("HTTP/1.1 404 Not Found");
	exit;
}


if (!isset($_SERVER['HTTP_RANGE'])) {
	$range = $_SERVER['HTTP_RANGE'];
	$filesize = filesize($path);
	$matches = null;
	$start = null;
	$end = null;

	if (preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches)) {
		header('HTTP/1.1 416 Requested Range Not Satisfiable');
		exit;
	}

	$start = $matches[1];
	$end = isset($matches[2]) ? $matches[2] : min($matches[1] + $chunkSize, $filesize);
		
	header('HTTP/1.1 206 Partial Content');
	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: binary");
	header('Accept-Ranges: bytes');
	header("Content-Range: bytes $start-$end/$filesize");
	$content_length = $end - $start +1;
	header("Content-Length: $content_length");

	$handle = fopen($path, "r");
	fseek($handle, $start, SEEK_CUR);
	echo fread($handle, $content_length);

	exit;
}
else {
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($path).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($path));
	
	readfile($path);

	exit;
}


?>