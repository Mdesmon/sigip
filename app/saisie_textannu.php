<?php
	session_start();
	include '../includes/global_include.php';

	/* SECURITE */
	if (!Controls::isConnected() OR Controls::control_user([APPRENANT]))
	{
		header('Location: acceuil_user.php');
		exit();
	}

	$etablissements = EtablissementsManager::getAllEtablissements();
	$success = 0;

	if(!empty($_POST)) {
		$success = saisie_staff();
	}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<?php
		include '../includes/head.html';
	?>

    <title>Formulaire de saisie</title>

	<link rel="stylesheet" type="text/css" href="../node_modules/pickadate/lib/themes/classic.css">
	<link rel="stylesheet" type="text/css" href="../node_modules/pickadate/lib/themes/classic.date.css">
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/formulaire_saisie.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
		.success {
			border: none;
			margin-bottom: 12px;
			background-color: #a6ff79;
			background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .4) 100%);
		}
	</style>
  </head>
  <body>
  	<?php include '../includes/avatar.php'; ?>
	
    <h1>Formulaire de saisie</h1>

	<?php
		if ($success):
			?>
				<div class="container bulleDialogue success">
					<div class="icon div circle-right"></div>
					Votre saisi manuelle pour <?php echo $_POST['sn'] .' '. $_POST['givenname']; ?> a été enregistré avec succes
				</div>
			<?php
		endif;
	?>

	<div class="container fiche">
		<form id="form_textannu" action="" method="post">
			<legend>Inscription</legend>
			<?php include '../includes/formulaire_textannu.php'; ?>
		</form>
		
	</div><!-- .container -->

	<script src="../node_modules/atomjs/atom.js"></script>
    <script src="../node_modules/jquery/dist/jquery.min.js"></script>
	<script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="../node_modules/pickadate/lib/picker.js"></script>
	<script src="../node_modules/pickadate/lib/picker.date.js"></script>
	<script src="../js/form_textannu.js"></script>
  </body>
</html>
<?php
	function saisie_staff() {
		$staffData = array(
			'numen' => $_POST['numen'],
			'sn' => $_POST['sn'],
			'nompatro' => $_POST['nompatro'],
			'givenname' => $_POST['givenname'],
			'datenaissance' => $_POST['datenaissance_submit'],
			'codecivilite' => $_POST['codecivilite'],
			'title' => $_POST['title'],
			'rne' => $_POST['rne'],
			'finfonction' => isset($_POST['finfonction']) ? "FF" : null,
			'dateff' => $_POST['dateff_submit'] ? $_POST['dateff_submit'] : null
		);
		
		return StaffManager::create(new Staff($staffData));
	}
?>