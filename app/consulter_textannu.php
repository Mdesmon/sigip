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
	$staff = StaffManager::getAllStaff();
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

    <title>Consulter Textannu</title>

	<link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="../node_modules/pickadate/lib/themes/default.css">
	<link rel="stylesheet" type="text/css" href="../node_modules/pickadate/lib/themes/default.date.css">
    <link href="../css/textannu.css" rel="stylesheet">
    <link href="../css/consulter_textannu.css" rel="stylesheet">
    <link href="../css/animation.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
  	<?php include '../includes/avatar.php'; ?>
	
    <h1>Consulter Textannu</h1>

	<div class="container-fluid">
		<table id="table" class="table table-bordered table-striped table-condensed table-hover">
			<thead>
				<tr>
					<th>NUMEN</th>
					<th>SN</th>
					<th>GIVENNAME</th>
					<th>TITLE</th>
					<th>RNE</th>
				</tr>
			</thead>
			<tbody id="tbody">
				<?php
					foreach ($staff as $key=>$s):
						?>
							<tr index="<?php echo $key; ?>">
								<td><?php echo $s->numen(); ?></td>
								<td><?php echo $s->sn(); ?></td>
								<td><?php echo $s->givenname(); ?></td>
								<td><?php echo $s->title(); ?></td>
								<td><?php echo $s->rne(); ?></td>
							</tr>
						<?php
					endforeach;
				?>
			</tbody>
		</table>
	</div>
					
	<div id="voile"></div>

	<form id="form_textannu" class="container fiche" style="display:none;">
		<input type="hidden" id="index" name="index">
		<?php include '../includes/formulaire_textannu.php'; ?>
	</form>


<script src="../node_modules/atomjs/atom.js"></script>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="../node_modules/pickadate/lib/picker.js"></script>
<script src="../node_modules/pickadate/lib/picker.date.js"></script>
<script src="../js/form_textannu.js"></script>
<script>
	var staff = <?php echo json_encode($staff); ?>;

	onresize = function() {
		if (isDisplayed(form_textannu)) {
			resizeForm_textannu();
		}
	};

	onkeydown = function(e) {
		var key = getKey(e);

		if (key == "Escape") {
			hide(voile);
			hide(form_textannu);
		}
	};

	form_textannu.onsubmit = function() {
		form_textannu_valide();
		return false;
	};

	tbody.ondblclick = function(e) {
		var tr;
		
		if (e.target.tagName === "TD")
			tr = e.target.parentElement;
		else
			tr = e.target;
		
		showForm_textannu(tr.getAttribute("index"));
	};

	voile.onclick = function() {
		hide(this);
		hide(form_textannu);
	}

	function showForm_textannu(i) {
		show(voile);
		showBlock(form_textannu);
		resizeForm_textannu();
		
		var s = staff[i];

		index.value = i;
		numen.value = s.numen;
		sn.value = s.sn;
		nompatro.value = s.nompatro;
		givenname.value = s.givenname;
		datenaissance.value = s.datenaissance;
		codecivilite.value = s.codecivilite;
		title.value = s.title;
		rne.value = s.rne;
		finfonction.checked = (s.finfonction === "FF");
		dateff.disabled = !finfonction.checked;
		dateff.value = s.dateff;
	}

	function resizeForm_textannu() {
		form_textannu.style.height = "";

		if(form_textannu.offsetHeight > getInnerHeight()-20)
			form_textannu.style.height = (getInnerHeight()-20) + "px";

		centerOnScreen(form_textannu);
	}

	function form_textannu_valide() {
		var empty = true;
		var s = staff[index.value];
		var changes = {};

		if(numen.value !== s.numen) {
			changes.numen = numen.value;
			empty = false;
		}
		if(sn.value !== s.sn) {
			changes.sn = sn.value;
			empty = false;
		}
		if(nompatro.value !== s.nompatro) {
			changes.nompatro = nompatro.value;
			empty = false;
		}
		if(givenname.value !== s.givenname) {
			changes.givenname = givenname.value;
			empty = false;
		}
		if(datenaissance.value !== s.datenaissance) {
			changes.datenaissance = invertDateFormat(datenaissance.value);
			empty = false;
		}
		if(codecivilite.value !== s.codecivilite) {
			changes.codecivilite = codecivilite.value;
			empty = false;
		}
		if(rne.value !== s.rne) {
			changes.rne = rne.value;
			empty = false;
		}
		if(title.value !== s.title) {
			changes.title = title.value;
			empty = false;
		}
		if(finfonction.checked && s.finfonction === "") {
			changes.finfonction = "FF";
			empty = false;
		}
		else if(!finfonction.checked && s.finfonction === "FF") {
			changes.finfonction = "";
			empty = false;
		}
		if(dateff.value === "" && s.dateff !== null) {
			changes.dateff = null;
			empty = false;
		}
		else if(dateff.value !== "" && dateff.value !== s.dateff) {
			changes.dateff = invertDateFormat(dateff.value);
			empty = false;
		}
		
		if(!empty)
			ajax_modifyTextannu(s.id, changes)
		else {
			hide(form_textannu);
			hide(voile);
		}
	}

	var xhr_modifyTextannu = new XMLHttpRequest();
	
	xhr_modifyTextannu.onreadystatechange = function() {
		var xhr = xhr_modifyTextannu;
		
		if(xhr.readyState != xhr.DONE || xhr.status != 200)
			return false;
		
		window.location.reload();
	};
	
	function ajax_modifyTextannu(id, changes){
		var xhr = xhr_modifyTextannu;
	
		xhr.open("POST", "../ajax/modifyTextannu.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("id="+ id +"&changes="+ JSON.stringify(changes));
	}

</script>
</body>
</html>