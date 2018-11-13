<?php
	session_start();
	include '../includes/global_include.php';

	/* SECURITE */
	if (!Controls::isConnected() OR Controls::control_user([APPRENANT]))
	{
		header('Location: acceuil_user.php');
		exit();
	}

	$rnes = [];

	// Javascript regex
	$regex_name = "^[A-Za-zÀ-ÖØ-öø-ÿ \-]+$";
	$regex_date = "^[0-9]{2}/[0-9]{2}/[0-9]{4}$";
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

    <title>Formulaire d'inscription</title>

	<link rel="stylesheet" type="text/css" href="../node_modules/pickadate/lib/themes/classic.css">
	<link rel="stylesheet" type="text/css" href="../node_modules/pickadate/lib/themes/classic.date.css">
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/textannu.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
  </head>
  <body>
  	<?php include '../includes/avatar.php'; ?>
	
    <h1>Formulaire d'inscription</h1>

	<div class="container fiche">
		<form action="" method="post">
			<legend>Inscription</legend>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="lastname">Nom :</label>
						<input type="text" name="lastname" id="lastname" class="form-control" required="required" pattern="<?php echo $regex_name; ?>">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="birthname">Nom de naissance (si différent) :</label>
						<input type="text" name="birthname" id="birthname" class="form-control">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="name">Prénom :</label>
						<input type="text" name="name" id="name" class="form-control" required="required" pattern="<?php echo $regex_name; ?>" title="">
					</div>
					<div class="form-group">
						<label for="civilite">Civilité :</label>
						<select type="text" name="civilite" id="civilite" class="form-control" required="required" title="">
							<option disabled selected> -- Choisir une option -- </option>
							<option value="M">Monsieur</option>
							<option value="Mme">Madame</option>
							<option value="Mlle">Mademoiselle</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="date_birthday">Date de naissance :</label>
						<input type="date" name="date_birthday" id="date_birthday" class="form-control" value="" required="required" pattern="<?php echo $regex_date; ?>">
					</div>
					<div class="form-group">
						<label class="label-form">RNE :</label>
						<select type="text" name="civilite" id="civilite" class="form-control" title="">
							<option disabled selected> -- Choisir une option -- </option>
							<?php
								foreach ($rnes as $rne) {
									echo '<option value=""></option>' . PHP_EOL;
								}
							?>
						</select>
							
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
						<label class="label-form">Status :</label>
						<select type="text" name="civilite" id="civilite" class="form-control" required="required" title="">
							<option disabled selected> -- Choisir une option -- </option>
							<option value="CTR_GIP">Contractuel GIP</option>
							<option value="PERS_GIP">Personnel GIP</option>
						</select>
					</div>
				</div>
				
			</div>
			
			

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="date_fin_fonction">Fin de fonction (décocher si aucun):</label>
						<div class="input-group">
							<div class="input-group-addon">
								<input type="checkbox" id="date_fin_fonction_active" checked>
							</div>
							<input type="date" name="date_fin_fonction" id="date_fin_fonction" class="form-control" value="" required="required">
							
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
			formatSubmit: 'dd/mm/yyyy'
		});

		$('#date_birthday').pickadate({
			max: 'picker__day--today'
		});
		$('#date_fin_fonction').pickadate();
	</script>
  </body>
</html>