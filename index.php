<!DOCTYPE html>
<html>
<?php include_once("head.php"); ?>

<body class="light" onload="addEvents()">
	<?php include_once("Header.php"); ?>
	<div class="content">
		<button id="getUserLocation">Parking near my position</button>
		<input id="searchBox" type="text" value="Paris 1er Arrondissement"></input>
		<button id="getSearchParams"> <- Parking near a location</button>
		<button id="locationButton">Pan to current location</button>
		<div id="map"></div>
		<script
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M&callback=initMap&v=weekly"
			defer></script>
		<?php include_once("Footer.php"); ?>
	</div>
</body>

</html>