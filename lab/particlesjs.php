<?php
	session_start();
	include '../includes/global_include.php';
	$bdd = DB::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include '../includes/head.html';
	?>
    
    <title>Menu principale</title>

	<style>
		body {
            height:100%;
        }

        #particles-js {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
		
	</style>
</head>
<body>

<?php include '../includes/avatar.php'; ?>

<div id="particles-js"></div>

<script src="../node_modules/atomjs/atom.js"></script>
<script src="../node_modules/particles.js/particles.js"></script>
<script type="text/javascript">



/* particlesJS.load(@dom-id, @path-json, @callback (optional)); */
particlesJS.load('particles-js', '../assets/particules-wired.json', function() {
  console.log('callback - particles-js config loaded');
});
	

</script>
</body>
</html>