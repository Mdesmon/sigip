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

	// Javascript regex
	$regex_name = "^[A-Za-zÀ-ÖØ-öø-ÿ \-]+$";
	$regex_date = "^[0-9]{2}/[0-9]{2}/[0-9]{4}$";
	$regex_numen = "^(GIPFCIP|GIPCFAA|PEN)+([A-Z0-9]){5,7}$";

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
		<form action="" method="post">
			<legend>Inscription</legend>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="lastname">Nom :</label>
						<input type="text" name="sn" id="lastname" class="form-control" required="required" pattern="<?php echo $regex_name; ?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="birthname">Nom de naissance (si différent) :</label>
						<input type="text" name="nompatro" id="birthname" class="form-control">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="name">Prénom :</label>
						<input type="text" name="givenname" id="name" class="form-control" required="required" pattern="<?php echo $regex_name; ?>" title="">
					</div>
					<div class="form-group">
						<label for="civilite">Civilité :</label>
						<select type="text" name="codecivilite" id="civilite" class="form-control" required="required" title="">
							<option disabled selected> -- Choisir une option -- </option>
							<option value="M">Monsieur</option>
							<option value="MM">Madame</option>
							<option value="MME">Mademoiselle</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="date_birthday">Date de naissance :</label>
						<input type="date" name="datenaissance" id="date_birthday" class="form-control" value="" required="required" pattern="<?php echo $regex_date; ?>">
					</div>
				</div>
				
			</div>

		

			<!-- <div class="form-group">
				<label class="label-form">
					Status :
					<div class="radio">
						<label for="" class="radio">
							<input type="radio" name="" id="">
							Contractuel GIP
						</label>
					</div>
					<div class="radio">
						<label for="" class="radio">
							<input type="radio" name="" id="">
							Personnel GIP
						</label>
					</div>
				</label>
			</div> -->


			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="numen">NUMEN :</label>
						<input type="text" name="numen" id="numen" class="form-control" required="required" pattern="<?php echo $regex_numen; ?>" title="">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="label-form">RNE :</label>
						<select type="text" name="rne" id="rne" class="form-control" required="required" title="">
							<option disabled selected> -- Choisir une option -- </option>
							<?php
								foreach ($etablissements as $e) {
									printf('<option value="%s">%s %s</option>', $e->rne(), $e->name(), $e->rne());
								}
							?>
						</select>
					</div>
				</div>
			</div>
			

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="label-form">Status :</label>
						<select type="text" name="title" id="title" class="form-control" required="required" title="">
							<option disabled selected> -- Choisir une option -- </option>
							<option value="CTR_GIP">Contractuel GIP</option>
							<option value="PERS_GIP">Personnel GIP</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="date_fin_fonction">Fin de fonction (décocher si aucun):</label>
						<div class="input-group">
							<div class="input-group-addon">
								<input type="checkbox" id="date_fin_fonction_active" name="finfonction" checked>
							</div>
							<input type="date" id="date_fin_fonction" name="dateff" class="form-control" required="required">
						</div>
					</div>
				</div>
			</div>

			<button class="pull-right btn btn-primary" type="submit">Envoyer</button>

		</form>
		
	</div><!-- .container -->

	<script src="../node_modules/atomjs/atom.js"></script>
    <script src="../node_modules/jquery/dist/jquery.min.js"></script>
	<script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="../node_modules/pickadate/lib/picker.js"></script>
	<script src="../node_modules/pickadate/lib/picker.date.js"></script>
	<script>
		var form = document.getElementsByTagName('form')[0];
		
		form.onsubmit = function() {
			if(date_birthday.value === "") {
				date_birthday.click();
				return false;
			}

			if(date_fin_fonction_active.checked && date_fin_fonction.value === "") {
				date_fin_fonction.click();
				return false;
			}
		};

		date_fin_fonction_active.onchange = function() {
			date_fin_fonction.disabled = !this.checked;
		};

		/* Pickadate */

		$.extend($.fn.pickadate.defaults, {
			selectYears: true,
			// Translations
			monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
			today: 'Aujourd\'hui',
			clear: 'Effacer',
			close: 'Fermer',
			labelMonthNext: 'Suivant',
			labelMonthPrev: 'Précédent',
			// Format
			format: 'dd/mm/yyyy',
			formatSubmit: 'yyyy/mm/dd'
		});

		$('#date_birthday').pickadate({
			max: 'picker__day--today'
		});
		$('#date_fin_fonction').pickadate();
	</script>
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
		
		error_log(print_r($_POST, TRUE), 0);
		return StaffManager::create(new Staff($staffData));
	}
?>