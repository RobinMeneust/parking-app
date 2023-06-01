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

<body>
	<?php include_once("Header.php"); ?>

	<div class="content">
		<div class="graphsContainer">
			<div id="graphLegend">
				<b style="color:blue; font-size:40px">-</b><span class="legendPredict"> : Valeurs réelles</span><br>
				<b style="color:red; font-size:40px">-</b><span class="legendPredict"> : Valeurs prédites</span>
			</div>
			<canvas class="graph" id="expensesPredict" style="height:500px;width:800px"></canvas>
		</div>
	</div>
	<?php include_once("Footer.php"); ?>
	<script>window.onload = async function() { await predict; }</script>
</body>
</html>