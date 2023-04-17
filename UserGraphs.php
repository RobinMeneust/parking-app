<?php session_start();

if(!isset($_SESSION['VAR_profil'])){
	header('location:../registerLogin.php?message=Veuillez vous connecter');
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<title>Détails Profil</title>
	<?php include_once("head.php"); ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
	<script src="JS/userGraphs.js"></script>
</head>

<body class="light" onload="initialize()">
	<?php include_once("Header.php"); ?>

	<div class="content">
		<div class="horizontalMenu">
			<input id="yearGraph" name="yearGraph" style="text-align:center;" class="menuButton rectangular" type="number" value="2023" min="1900" max="2050">
			<button id="refreshDate" style="text-align:center;" class="menuButton rectangular">Valider</button>
		</div>
		<div class="graphsContainer">
			<canvas id="visits"></canvas>
			<canvas id="expenses"></canvas>
		</div>
	</div>

	<?php include_once("Footer.php"); ?>
</body>
</html>