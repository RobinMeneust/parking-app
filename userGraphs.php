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

<body>
	<?php include_once("Header.php"); ?>

	<div class="content">
		<div class="horizontalMenu">
			<input class="date_input" id="yearGraph" name="yearGraph" style="text-align:center;" type="number" value="2023" min="1900" max="2050">
			<a id="refreshDate" class="detailsButtons" style="text-align:center; color:black; --clr:#1e9bff"><span>Valider</span><i></i></a>
		</div>
		<div class="graphsContainer">
			<canvas class="graph" id="visits"></canvas>
			<canvas class="graph" id="expenses"></canvas>
		</div>
	</div>

	<?php include_once("Footer.php"); ?>
	<script>window.onload = initialize;</script>
</body>
</html>