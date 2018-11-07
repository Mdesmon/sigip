<?php
    session_start();
    include '../includes/config.php';
    
    /* SUPPRIME LES VARIABLES SESSION LIEES A L'APPLI WEB */
    foreach($_SESSION[APP_NAME] as $key => $value) {
        unset($_SESSION[APP_NAME][$key]);
    }
    

    header('Location: ../login.php');
?>
