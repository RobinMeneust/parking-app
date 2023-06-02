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
	<body>
		<?php include_once("Header.php"); ?>
		<div class="contentAddToHistory">
			

			<div id="bandeauAddToHistory" >
				<h1 id="titleAddHistory">Ajouter ce parking à votre historique</h1>
        	</div>
			<form>
				<div class="input-container">
					<input required id="expenses" type="number" class="text-input" name="expenses" value="0" min="0", max="100", step="0.5">
					<label for="expenses" class="label">Argent dépensé (€)</label>
				</div>
				<div class="input-container">
					<input required id="name" type="text" class="text-input" name="name" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["name"]) && $_SESSION["currentParking"]["name"] != "" && $_SESSION["currentParking"]["name"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["name"] ."\" readonly";} else {echo "value=\"\" placeholder=\"\"";} ?> >
					<label for="nom" class="label">Nom du parking</label>
				</div>
				<div class="input-container">
					<input id="houseNumber" type="text" class="text-input" name="houseNumber" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["houseNumber"]) && $_SESSION["currentParking"]["address"]["houseNumber"] != "" && $_SESSION["currentParking"]["address"]["houseNumber"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["houseNumber"] ."\" readonly";} else {echo "value=\"\" placeholder=\"\"";} ?> >
					<label for="houseNumber" class="label">N° de rue</label>
				</div>
				<div class="input-container">
					<input id="street" type="text" class="text-input" name="street" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["street"]) && $_SESSION["currentParking"]["address"]["street"] != "" && $_SESSION["currentParking"]["address"]["street"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["street"] ."\" readonly";} else {echo "value=\"\" placeholder=\"\"";} ?> >
					<label for="street" class="label">Nom de rue</label>
				</div>
				<div class="input-container">
					<input id="city" type="text" class="text-input" name="city" <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["city"]) && $_SESSION["currentParking"]["address"]["city"] != "" && $_SESSION["currentParking"]["address"]["city"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["city"] ."\" readonly";} else {echo "value=\"\" placeholder=\"\"";} ?> >
					<label for="city" class="label">Ville</label>
				</div>
				<div class="input-container">
					<input id="country" type="text" class="text-input" name="country"  <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["country"]) && $_SESSION["currentParking"]["address"]["country"] != "" && $_SESSION["currentParking"]["address"]["country"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["country"] ."\" readonly";} else {echo "value=\"\" placeholder=\"\"";} ?> >
					<label for="country" class="label">Pays</label>
				</div>
				<div class="input-container">
					<input id="postalCode" type="number" min="0" max="99999" class="text-input" name="postalCode"  <?php if(isset($_SESSION["currentParking"]) && isset($_SESSION["currentParking"]["address"]) && isset($_SESSION["currentParking"]["address"]["postalCode"]) && $_SESSION["currentParking"]["address"]["postalCode"] != "" && $_SESSION["currentParking"]["address"]["postalCode"] != "undefined") {echo "value=\"".$_SESSION["currentParking"]["address"]["postalCode"] ."\" readonly";} else {echo "value=\"\" placeholder=\"\"";} ?> >
					<label for="postalCode" class="label">Code postal</label>
				</div>
			</form>
			
			<p id="infoBox"></p>
			<button id="btnAddToHistory" onclick="addToHistory()">Ajouter</button>
			<div class="popupAddToHistory" id="popupAddToHistory">
				<img src="./assets/img/iconeCheckAddToHistory.png" alt="" />
				<h2>L'ajout du parking a été effectué</h2>
				<p>Vous pouvez retrouver tous vos parkings préférés dans votre historique</p>
				<button type="button" id="btnClosePopup">OK</button>
			</div>			
		</div>
		<?php include_once("Footer.php"); ?>
		<script>window.onload = getCurrentParkingFromSession;</script>
	</body>

</html>