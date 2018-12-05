<?php
	session_start();
	include '../includes/global_include.php';

	/* SECURITE */
	if (!Controls::isConnected() OR Controls::control_user([APPRENANT]))
	{
		header('Location: acceuil_user.php');
		exit();
	}

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
    <link href="../css/textannu.css" rel="stylesheet">
    <link href="../css/consulter_textannu.css" rel="stylesheet">

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
		<table id="table" class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th>NUMEN</th>
					<th>SN</th>
					<th>GIVENNAME</th>
					<th>TITLE</th>
					<th>RNE</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($staff as $s):
						?>
							<tr>
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
	



<script src="../node_modules/atomjs/atom.js"></script>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
	var staff = <?php echo json_encode($staff); ?>;

	document.body.onwheel = function(e) {
		console.log(wheelDirection(e));
		
	};

</script>
</body>
</html>