<?php
    /* AJOUT SUR FICHIER */
    $file = fopen('../error.log', 'a+');

    $time = date("d-m-Y H:i:s");
    $data = $_POST['data'];
    $type = $_POST['type'];
    $occurence = $_POST['occurence'];

    if($type === 'string')
        fputs($file, $time . ' Occurence : ' . $occurence  . ' ' . $data . PHP_EOL);
    else if ($type === 'json') {
        $json = json_decode($data);

        fputs($file, $time . ' Occurence : ' . $occurence . PHP_EOL);

        foreach ($json as $key => $value) {
            fputs($file, '  ' . $key . ' : ' . $value . PHP_EOL);
        }
    }
    else {
        fputs($file, $time . ' Occurence : ' . $occurence  . ' DONNEES NON DISPONIBLES ' . PHP_EOL);
    }
    
    fclose($file);
?>