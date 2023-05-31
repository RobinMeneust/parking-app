<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php include_once("head.php"); ?>
		<script src="./JS/index.js"></script>
		<script src="./JS/global.js"></script>
		<script src="./JS/map.js"></script>
		<script src="./JS/getParkingData.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATRnNNrouwTPPFjX_C5u3MRivuWj2P22M&callback=initMap&v=weekly"	defer></script>
	</head>

	<body class="light" onload="initializeForms();">
		<?php include_once("Header.php"); ?>

		<?php 
			if (isset($_GET["message"]) && !empty($_GET["message"]) ) {
				$error_msg = htmlspecialchars($_GET["message"]);
				include("./PHP/errorMessage.php");
			}
		?>
		<div class="content">	
			<i class="fa-solid fa-bars menuButton" onclick="toggleMenuVisibility()"></i>
			<div class="sideBar column">
				<div class="sideBar hidden light-mode" id="sideBarContent">
					<h2>Filtres</h2>
					<br>
					<form>
						<legend>Départements ou arrondissements</legend>
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
					<a class="detailsButtons" style="position:absolute; color:black; --clr:#1e9bff" onclick="getSearchFilters()"><span>Appliquer mes filtres</span><i></i></a>
				</div>
			</div>

			<div class="row">
				<div class="column" id="map"></div>
				<span id="searchMsgBox"></span>
				<div class="row" id="searchDetailsSideBar">				
					<h2 id="titleParkSelected">Détails du parking sélectionné</h2>
					<table id="selectedParkingTable">
						<tr>
							<th>Adresse</th>
							<th>Places disponibles</th>
							<th>Horaires</th>
							<th>Paiement</th>
						</tr>
						<tr id="selectedParkingTableRowData">
							<td id="addressSelectedParking" data-label="Adresse"></td>
							<td id="nbSlotsSelectedParking" data-label="Places disponibles"></td>
							<td id="openingHoursSelectedParking" data-label="Horaires"></td>
							<td id="paymentSelectedParking" data-label="Paiement"></td>
						</tr>
					</table>
                    
                    <table class="instruction-wrapper" cellspacing="0" cellpadding="0" border="0" style="width:100%;">
                        <tr>
                            <td>
                            <table cellspacing="0" cellpadding="1" border="1" style="width:100%;">
                                <tr>
                                    <th style="width: 635px;">Instructions</th>
                                    <th style="width: 85px;">Distance</th>
                                </tr>
                            </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <div style="height:300px; overflow:auto;">
                                <table class="instruction" cellspacing="0" cellpadding="1" border="1" >
                                </table>  
                            </div>
                            </td>
                        </tr>
                    </table>
				</div>
			</div>
		</div>

		<?php include_once("Footer.php"); ?>

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