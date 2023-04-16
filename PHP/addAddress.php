<?php
session_start();

// houseNumber, street, city, country, postalCode, lat, lng
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["h"]) && isset($_GET["s"]) && isset($_GET["i"]) && isset($_GET["o"]) && isset($_GET["p"]) && isset($_GET["a"]) && isset($_GET["n"])){
	// Connect to database
	$host = 'db';
	$user = 'MYSQL_USER';
	$pass = 'MYSQL_ROOT_PASSWORD';
	$database = 'usersdata';

	$houseNumber = $_GET["h"];
	$street = $_GET["s"];
	$city = $_GET["i"];
	$country = $_GET["o"];
	$postalCode = $_GET["p"];
	$lat = $_GET["a"];
	$lng = $_GET["n"];

	$link = mysqli_connect($host,'root', $pass, $database);

	// Check connection
	if (!$link) {
		die("Erreur de connexion à la base de données : " . mysqli_connect_error());
	}

	$query = "SELECT idAddress FROM Addresses WHERE houseNumber = ".$houseNumber." AND street = \"".$street."\" AND city = \"".$city."\" AND country = \"".$country."\" AND postalCode = ".$postalCode." AND lat = ".$lat." AND lng = ".$lng.";";
	if($resultSQL = mysqli_query($link,$query)){
		if(mysqli_num_rows($resultSQL)>0){
			// It's a duplicate, the value already exists
			if($tab = mysqli_fetch_assoc($resultSQL)){
				echo $tab['idAddress'];
				exit;
			} else{
				exit;
			}
		}
	} else{
		exit;
	}
	
	$query = "INSERT INTO Addresses VALUES(NULL,".$houseNumber.",\"".$street."\",\"".$city."\",\"".$country."\",".$postalCode.",".$lat.",".$lng.");";

	if($resultSQL = mysqli_query($link,$query)) {
		echo mysqli_insert_id($link);
		mysqli_close($link);
	}
}
?>