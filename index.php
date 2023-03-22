<!DOCTYPE html>
<html>
<head>
	<?php include_once("head.php"); ?>
	<script src="./index.js"></script>
	<script src="./getParkingData.js"></script>
</head>
<body class="light" onload="addEvents(); initializeForms();">
	<?php include_once("Header.php"); ?>
	<div class="content">
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M&callback=initMap&v=weekly"	defer></script>
		<button id="locationButton">Pan to current location</button>
		<br>
		<button class="menuButton" onclick="toggleMenuVisibility()"><i class="fa-solid fa-filter"></i></button>
		
		<div class="sideBar column">
			<div class="sideBar hidden" id="sideBarContent"><h2>Filtres</h2>
				<form>
					<legend>Recherche par départements ou arrondissements</legend>
					<select id="selectSearchParams">
						<optgroup label="Paris - Arrondissement" id="arrondissementsOptgroup"></optgroup>
						<optgroup label="Département d'Île-de-France" id="departementsOptgroup"></optgroup>
					</select>
					<br>
					<label name="nbMaxSlider">Nombre maximum de parkings à afficher : </label>

					<input id="nbMaxSlider" name="nbMaxSlider" type="range" oninput="this.nextElementSibling.value = this.value;" value="10" min="1" max="200" step="1">
					<output>10</output> parkings
				</form>
				<button id="getSearchParams"><i class="fa-solid fa-magnifying-glass"></i></button>
				<br><br>
				<form>
					<legend>Recherche en fonction de notre position</legend>
					<label name="distanceSlider">Distance: </label>
					<input id="distanceSlider" name="distanceSlider" type="range" oninput="this.nextElementSibling.value = this.value;" value="1" min="0.5" max="10" step="0.5">
					<output>1</output> km
				</form>
				<button id="getUserLocation"><i class="fa-solid fa-magnifying-glass"></i></button>
			</div>
		</div>
		<div class="row">
			<div class="column" id="map"></div>
			<div class="column" id="searchDetailsSideBar">
				<h2>Détail du parking sélectionné</h2>
				<table id="selectedParkingTable">
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
</body>

</html>