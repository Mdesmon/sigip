<?php
	session_start();
	include '../includes/global_include.php';
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		include '../includes/head.html';
	?>
	<link rel="stylesheet" type="text/css" href="../css/backgrounds.css">
	<link rel="stylesheet" type="text/css" href="../css/animation.css">
    
    <title>Menu principale</title>

	<style>
		body {
			width: 100%;
			height: 100%;
			padding-top: 90px;
			overflow: hidden;
		}
		#menuPrincipale {
			width: 260px;
			height: 100%;
			box-sizing: border-box;
			text-align: center;
			position: fixed;
			left: 0;
			top: 0;
		}
		#page {
			width: 100%;
			height: 100%;
			padding-left: 260px;
			box-sizing: border-box;
			overflow-y: auto;
		}
		#menu_animations {
			width: 250px;
		}
		#menu_buttons {
			width: 250px;
		}
		.menu {
			margin: auto;
			text-align: center;
		}
		.container {
			width: 100%;
			box-sizing: border-box;
			padding: 10px;
		}
		.player .btnVst {
			border-radius: 0;
			border-width: 1px 0 1px 1px;
		}
		.player .btnVst.first {
			border-top-left-radius: 5px;
			border-bottom-left-radius: 5px;
		}
		.player .btnVst.last {
			border-top-right-radius: 5px;
			border-bottom-right-radius: 5px;
			border-width: 1px;
		}
		
	</style>
</head>
<body>

<?php include '../includes/avatar.php'; ?>

<div id="menuPrincipale" class="menu2 vst2">
		
	<div class="space25"></div>

	<div>LOGIN :</div>
	<div class="btnVst icon login"></div>
	<div class="btnVst icon logout"></div>
	<div class="btnVst icon connexion"></div>
	
	<div class="space"></div>
	<div>PLAYER :</div>

	<div class="player">
		<div class="btnVst icon play first"></div><div class="btnVst icon pause"></div><div class="btnVst icon stop"></div><div class="btnVst icon loop last"></div>
		<div class="space"></div>
		<div class="btnVst icon previous first"></div><div class="btnVst icon rewind"></div><div class="btnVst icon fastForward"></div><div class="btnVst icon next"></div><div class="btnVst icon eject last"></div>
	</div>
	
</div>


<div id="page">
	<div id="menu_animations" class="menu vst4">
		Animations :
		<select id="selectAnimation">
			<option value="fadeIn">Fondu</option>
			<option value="zoomIn">Zoom</option>
			<option value="rotateIn">Newspaper</option>
			<option value="slit">Slit</option>
			<option value="slideInRight">Slide Droite</option>
			<option value="slideInLeft">Slide Gauche</option>
			<option value="slideInUp">Slide Haut</option>
			<option value="slideInDown">Slide Bas</option>
			<option value="flipInX">Flip X</option>
			<option value="flipInY">Flip Y</option>
			<option value="fall">Tomber</option>
			<option value="rotate">Rotation</option>
			<option value="blink">Flash</option>
			<option value="rebond">Rebond</option>
			<option value="rebondY">Rebond 2D</option>
			</select>
		</div>

		<div class="space25"></div>

		<div style="width:532px;margin:auto;position:relative;perspective:1000px;perspective-origin: 50% 50%;">
			<div id="menuButtons" class="menu glass" onselectstart="event.preventDefault();">
				<p class="titre5">BUTTONS :</p>
				<div class="space"></div>
				<div class="btnBlanc">Blanc</div>
				<div class="btnFlat">Flat</div>
				<div class="btnRetro">Retro</div>
				<div class="btnDouble">Double</div>
				<div class="btnRounded">Rounded</div>
				<div class="btnMetal">Metal</div>
				<div class="btnBlue">Blue</div>
				<div class="btnSteam">Steam</div>
				<div class="btnHelly">Helly</div>
				<div class="btnSpringtime">Springtime</div>
				<div class="btnWeki">Weki</div>
				<br />
				<br /><div class="btnVst2">Vst 2</div><div class="btnVst2">Vst 2</div><div class="btnVst3">Vst 3</div><div class="btnVst3">Vst 3</div><!--
			--><br /><div class="btnVst2">Vst 2</div><div class="btnVst2">Vst 2</div><div class="btnVst3">Vst 3</div><div class="btnVst3">Vst 3</div><br />
				<br />
				<div class="btnFaceB">FaceB</div>
				<div class="btnVst">Vst</div>
				<div class="btnVst4">Vst 4</div>
				<div class="btnVst5">Vst 5</div>
				<div class="btn3D">3D</div>
				<br />
				<div class="btnModerne">Moderne</div>
				<div class="btnGlass">Glass</div>
				<div class="btnGlass2">Glass 2</div>
				<div class="btnOutline">Outline</div>
			</div>
		</div>

		<div class="container">
			<div class="bulleDialogue">Bulle de dialogue par defaut</div>
			<div class="space"></div>
			<div class="bulleDialogue green">Bulle de dialogue verte</div>
			<div class="space"></div>
			<div class="bulleDialogue red">Bulle de dialogue rouge</div>
			<div class="space"></div>
			<div class="flat sharp noirTransparent">Flat Noir Transparent</div>
			<div class="space"></div>
			<div class="flat sharp blancTransparent">Flat Blanc Transparent</div>
			<div class="space"></div>
			<div class="flat">Flat</div>
			<div class="space"></div>
			<div class="steam">Steam</div>
			<div class="space"></div>
			<div class="double">Double</div>
			<div class="space"></div>
			<div class="springtime">Springtime</div>
			<div class="space"></div>
			<div class="helly grey">Helly</div>
			<div class="space"></div>
			<div class="glass">Glass</div>
			<div class="space"></div>
			<div class="glass2">Glass 2</div>
			<div class="space"></div>
			<div class="faceB">FaceB</div>
			<div class="space"></div>
			<div class="outline">Outline</div>
			<div class="space"></div>
			<div class="vst">VST</div>
			<div class="space"></div>
			<div class="vst2">VST2</div>
			<div class="space"></div>
			<div class="vst3">VST3</div>
			<div class="space"></div>
			<div class="vst4">VST4</div>
			<div class="space"></div>
			<div class="vst5">VST5</div>
			<div class="space"></div>
			<div class="retro">Retro</div>
			<div class="space"></div>
			<div class="retro2">Retro2</div>
		</div>
	</div>
</div>
<script src="../node_modules/atomjs/atom.js"></script>
<script type="text/javascript">


	selectAnimation.onchange = function() {
		menuButtons.style.animation = selectAnimation.value + " 0.8s";
	};

</script>
</body>
</html>