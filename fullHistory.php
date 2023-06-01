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

	<div class="content">
		<table id="tableFullHistory"></table>
	</div>

	<?php include_once("Footer.php"); ?>
</body>
</html>