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
	<script src="JS/fullHistory.js"></script>
</head>

<body>
	<?php include_once("Header.php"); ?>

	<div id="bannerFullHistory">
		<h1 id="titleAddHistory">Liste détaillée des parkings utilisés</h1>
	</div>
	<div id="contentFullHistory">
		<a class="detailsButtons" style="--clr:#6eff3e" onclick="createFullTable();"><span>Liste complète</span><i></i></a>
		<a class="detailsButtons" style="--clr:#6eff3e" onclick="createTableByParking();"><span>Liste par parking</span><i></i></a>
		<table id="tableFullHistory"></table>
	</div>

	<?php include_once("Footer.php"); ?>
</body>
</html>