<!DOCTYPE html>
<html>
<?php include_once("head.php"); ?>

<body class="light" onload="addEvents()">
	<?php include_once("Header.php"); ?>
	<div class="content">
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M&callback=initMap&v=weekly"	defer></script>

		<button id="getUserLocation">Parking near my position</button>
		<input id="searchBox" type="text" value="Paris 1er Arrondissement"></input>
		<button id="getSearchParams"> <- Parking near a location</button>
		<button id="locationButton">Pan to current location</button>
		<br>
		<button class="menuButton" onclick="toggleMenuVisiblity()"><i class="fa-solid fa-filter"></i></button>
		
		<div class="sideBar column">
			<div class="sideBar hidden" id="sideBarContent">Filtres</div>
		</div>
		<div class="row">
			<div class="column" id="map"></div>
			<div class="column" id="searchDetailsSideBar">
				<h2>Détail du parking sélectionné</h2>
				<p id="selectedParkingData"></p>
			</div>
		</div>
			<?php include_once("Footer.php"); ?>
	</div>
</body>

</html>