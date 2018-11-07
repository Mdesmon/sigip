<?php
    include "../includes/global_include.php";
    include "../vendor/autoload.php";
    include "../includes/envoi_email.php";

    $user = UsersManager::getUserByUsername($_POST['username']);

    if($user === "WRONG USERNAME")
        echo "Le nom d'utilisateur " . $_POST['username'] . " n'existe pas";
    else {
        envoi_mail_recuperation($user);
        echo "OK";
    }
?>