<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;


	function envoi_mail($destinataires, $copiesCachees, $subject, $msgHTML) {
		if(!MAIL_ENABLED)
			return "MAIL DISABLED";
		
		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		// Set encoding. Must be after $mail = new PHPMailer();
		$mail->CharSet = MAIL_CHARSET;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = MAIL_HOST;
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = MAIL_PORT;
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		//Username to use for SMTP authentication
		$mail->Username = MAIL_USERNAME;
		//Password to use for SMTP authentication
		$mail->Password = MAIL_PASSWORD;
		//Set who the message is to be sent from
		$mail->setFrom(MAIL_FROM, MAIL_NAME);
		//Set an alternative reply-to address
		$mail->addReplyTo(MAIL_FROM, MAIL_NAME);
		//Set who the message is to be sent to
		foreach ($destinataires as $adresse) {
			$mail->addAddress($adresse);
		}
		//Indique des adresses en copie cachées
		foreach ($copiesCachees as $adresse) {
			$mail->addBCC($adresse);
		}
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		//$mail->msgHTML($msgHTML);
		$mail->msgHTML($msgHTML);
		//Replace the plain text body with one created manually
		//$mail->AltBody = 'This is a plain-text message body';
		//Attachment
		
		//send the message, check for errors
		if (!$mail->send()) {
			error_log(print_r("Mailer Error: " . $mail->ErrorInfo, TRUE), 0);
		}
	}
	
	function envoi_mail_inscription($user, $password) {
		$destinataires = array($user->email());
		$copiesCachees = array();
		
		$msgHTML = '
			<html>
			<head></head>
			<body>
				'. addImgBase64('logo_complet_mini') .'
				<br />
				<br />
				<br />
				Bonjour '. $user->name() .' '. $user->lastName() .',
				<br />
				Vous êtes maintenant inscrit sur Web Atlas.
				<br /><br />
				Vos identifiants sont les suivants :<br /><br />
				- Nom d\'utilisateur : '. $user->username() .'<br />
				- Mot de passe : '. $password .'<br />


			</body>
			</html>
		';
		
		envoi_mail($destinataires, $copiesCachees, 'Bienvenue sur Web Atlas', $msgHTML);
	}

	function envoi_mail_recuperation($user) {
		$link = $_SERVER["HTTP_REFERER"] . '?user='. $user->username() .'&code='. $user->folder();

		$destinataires = array($user->email());
		$copiesCachees = array();
		
		
		$msgHTML = '
			<html>
			<head></head>
			<body>
				'. addImgBase64('logo_complet_mini') .'
				<br />
				<br />
				<br />
				Bonjour '. $user->name() .' '. $user->lastName() .'
				<br />
				<br />
				Vous pouvez réinitialiser votre mot de passe en cliquant sur le lien ci-dessous :
				<br />
				<br />
				<a href="'. $link .'">Réinitialiser mot de passe</a>

			</body>
			</html>
		';
		
		envoi_mail($destinataires, $copiesCachees, 'Réinitialisation de mot de passe', $msgHTML);
	}

	function envoi_mail_resetPassword($user, $password) {
		$destinataires = array($user->email());
		$copiesCachees = array();
		
		$msgHTML = '
			<html>
			<head></head>
			<body>
				'. addImgBase64('logo_complet_mini') .'
				<br />
				<br />
				<p>Bonjour '. $user->name() .' '. $user->lastName() .'.<br />Vous mot de passe sur Web Atlas.gretaformation.com a été réinitialisé :</p>
				<div>
					- Nom d\'utilisateur : '. $user->username() .'<br />
					- Nouveau mot de passe : '. $password .'<br />
				</div>
				<p>
					Conservez ces identifiants, vous en aurez besoin pour vous connecter à vos sessions de formation.<br />
					<br />
					Bien cordialement,<br />
					<br />
					l\'équipe Web Atlas.
				</p>
				<br />
			</body>
			</html>
		';
		
		envoi_mail($destinataires, $copiesCachees, 'Web Atlas - Réinitialisation de mot de passe', $msgHTML);
	}
	
?>