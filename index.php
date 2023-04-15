<?php
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<?php include_once("head.php"); ?>
		<script src="./JS/index.js"></script>
		<script src="./JS/global.js"></script>
		<script src="./JS/map.js"></script>
		<script src="./JS/getParkingData.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M&callback=initMap&v=weekly"	defer></script>
	</head>

	<body class="light" onload="initializeForms();">
		<?php include_once("Header.php"); ?>
		<?php 
			if (isset($_GET["message"]) && !empty($_GET["message"]) ) {
				$error_msg = htmlspecialchars($_GET["message"]);
				include("PHP/error_msg.php");
			}
		?>
		<div class="content">
			<br>
			<button class="menuButton" style="position:absolute;" onclick="toggleMenuVisibility()"><i class="fa-solid fa-filter"></i></button>
			
			<div class="sideBar column">
				<div class="sideBar hidden" id="sideBarContent">
					<h2>Filtres</h2>
					<br>
					<form>
						<legend>Départements ou arrondissements :</legend>
						<br>
						<select id="selectSearchParams">
							<option disabled selected value="null"></option>
							<optgroup label="Paris - Arrondissement" id="arrondissementsOptgroup"></optgroup>
							<optgroup label="Département d'Île-de-France" id="departementsOptgroup"></optgroup>
						</select>
						<br><br><hr><br>
						<label name="nbMaxSlider">Nombre maximum de parkings à afficher : </label>
						<br>
						<input id="nbMaxSlider" name="nbMaxSlider" type="range" oninput="this.nextElementSibling.value = this.value;" value="10" min="1" max="200" step="1">
						<output>10</output>
						<br><br>
						<label name="distanceSlider">Distance (en km) : </label>
						<input id="distanceSlider" name="distanceSlider" type="range" oninput="this.nextElementSibling.value = this.value;" value="1" min="0.5" max="10" step="0.5">
						<output>1</output>
					</form>
					<br>
					<button class="menuButton rectangular" style="position:absolute;" onclick="getSearchFilters()">Appliquer mes filtres</button>
				</div>
			</div>
			<div class="row">
				<div class="column" id="map"></div>
				<div class="column" id="searchDetailsSideBar">
					<h2>Détail du parking sélectionné</h2>
					<table id="selectedParkingTable">
						<span id="searchMsgBox"></span>
						<tr>
							<th>Adresse</th>
							<th>Nombre de places disponibles</th>
							<th>Horaires d'ouvertures</th>
							<th>Paiement</th>
						</tr>
						<tr id="selectedParkingTableRowData">
							<td id="addressSelectedParking"></td>
							<td id="nbSlotsSelectedParking"></td>
							<td id="openingHoursSelectedParking"></td>
							<td id="paymentSelectedParking"></td>
						</tr>
					</table>
				</div>
			</div>
				<?php include_once("Footer.php"); ?>
		</div>

		<?php
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$address = $_POST["search-address-text"];

				if(strlen($address) > 200){
					$address = "";
				}
			}

			if(isSet($address)){
				echo '<script>'."\n";
				echo 'let address = "'.$address.'";'."\n";
				echo 'if(address == ""){'."\n";
				echo '	console.log("chaîne vide ou trop de caractères");'."\n";
				echo '} else{'."\n";
				echo '	getParkingsNearAddress(address);'."\n";
				echo '}'."\n";
				echo '</script>'."\n";
			} 
			?>
	</body>
</html>
