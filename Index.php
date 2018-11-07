<?php
	session_start();

	include 'includes/constantes.php';
	include 'includes/config.php';
	include 'class/DB.php';
	include 'class/Controls.php';

	if (Controls::isConnected()) {
		if (Controls::control_user([APPRENANT])) {
			header('Location: app/acceuil_user.php');
		}

		header('Location: app/acceuil.php');
	}
	else {
		header('Location: login.php');
	}
