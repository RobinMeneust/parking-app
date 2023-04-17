<?php session_start(); 
if(!isset($_SESSION['VAR_profil'])){
	header('location:../registerLogin.php?message=Veuillez vous connecter');
}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php include_once("head.php"); ?>
		<script src="./JS/addToHistory.js"></script>
	</head>
	<body class="light" onload="getCurrentParkingFromSession();">
		<?php include_once("Header.php"); ?>
		<div class="content">
			<h2>Ajouter ce parking à votre historique</h2>
			<form>
				<div class="inputBox">
					<span>Argent dépensé (€)</span><br /> 
					<input required id="expenses" type="number" class="inputField" name="expenses" value="0" min="0", max="100", step="0.01">
				</div>
				<div class="inputBox">
					<span>Nom</span><br /> 
					<input required id="name" type="text" class="inputField" name="name" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["name"]) && $_SESSION["currentParking"]["name"] != "" && $_SESSION["currentParking"]["name"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["name"] ."\" readonly";} else {echo "value=\"\" placeholder=\"Nom du parking\"";} ?> >
				</div>
				<div class="inputBox">
					<span>N° de rue</span><br /> 
					<input id="houseNumber" type="text" class="inputField" name="houseNumber" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["houseNumber"]) && $_SESSION["currentParking"]["address"]["houseNumber"] != "" && $_SESSION["currentParking"]["address"]["houseNumber"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["houseNumber"] ."\" readonly";} else {echo "value=\"\" placeholder=\"N° de rue\"";} ?> >
				</div>
				<div class="inputBox">
					<span>Nom de rue</span><br /> 
					<input id="street" type="text" class="inputField" name="street" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["street"]) && $_SESSION["currentParking"]["address"]["street"] != "" && $_SESSION["currentParking"]["address"]["street"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["street"] ."\" readonly";} else {echo "value=\"\" placeholder=\"Nom de rue\"";} ?> >
				</div>
				<div class="inputBox">
					<span>Ville</span><br /> 
					<input id="city" type="text" class="inputField" name="city" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["city"]) && $_SESSION["currentParking"]["address"]["city"] != "" && $_SESSION["currentParking"]["address"]["city"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["city"] ."\" readonly";} else {echo "value=\"\" placeholder=\"Ville\"";} ?> >
				</div>
				<div class="inputBox">
					<span>Pays</span><br /> 
					<input id="country" type="text" class="inputField" name="country"  <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["country"]) && $_SESSION["currentParking"]["address"]["country"] != "" && $_SESSION["currentParking"]["address"]["country"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["country"] ."\" readonly";} else {echo "value=\"\" placeholder=\"Pays\"";} ?> >
				</div>
				<div class="inputBox">
					<span>Code postal</span><br /> 
					<input id="postalCode" type="number" min="0" max="99999" class="inputField" name="postalCode"  <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["postalCode"]) && $_SESSION["currentParking"]["address"]["postalCode"] != "" && $_SESSION["currentParking"]["address"]["postalCode"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["postalCode"] ."\" readonly";} else {echo "value=\"\" placeholder=\"Code postal\"";} ?> >
				</div>
			</form>
			<button onclick="addToHistory()">Ajouter</button>
			<p id="infoBox"></p>
		</div>
		<?php include_once("Footer.php"); ?>
	</body>

</html>