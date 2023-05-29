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
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="JS/predictExpenses.js"></script>
</head>

<body class="light" onload="predict()">
	<?php include_once("Header.php"); ?>

	<div class="content">
		<div class="graphsContainer">
			<div id="graphLegend">
				<b style="color:blue; font-size:40px">-</b> : Valeurs réelles<br>
				<b style="color:red; font-size:40px">-</b>  : Valeurs prédites
			</div>
			<canvas id="expensesPredict" style="height:500px;width:800px"></canvas>
		</div>
	</div>
	<?php include_once("Footer.php"); ?>
</body>
</html>