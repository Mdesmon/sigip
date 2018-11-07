<?php
    include "../includes/global_include.php";
    include "../vendor/autoload.php";
    include "../includes/envoi_email.php";

    if( empty($_POST['username']) OR empty($_POST['code']) OR empty($_POST['newPassword']) )
        return false;

    $user = UsersManager::getUserByUsername($_POST['username']);
    $executed = FALSE;

    if($user !== "WRONG USERNAME") {
        if($user->folder() === $_POST['code']) {
            UsersManager::changePassword($user, $_POST['newPassword'], true);
            $executed = TRUE;
        }
    }

    if($executed)
        echo "OK";
    else
        echo "Une erreur est survenue. Le mot de passe n'a pas pu être changé";
?>