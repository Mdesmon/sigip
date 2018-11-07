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
            position: absolute;
        }
		
	</style>
</head>
<body>

<?php include '../includes/avatar.php'; ?>

<div id="container-particles"></div>

<script src="../node_modules/atomjs/atom.js"></script>
<script src="../node_modules/three/build/three.min.js"></script>
<script type="text/javascript">

var container = document.getElementById("container-particles");
var scene = new THREE.Scene();
var renderer = new THREE.WebGLRenderer();
var geometrie = new THREE.geometri
var material;
var camera;





function init() {

}

function animate() {

}

</script>
</body>
</html>