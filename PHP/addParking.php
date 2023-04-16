<?php
session_start();

// idAddress, name
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["i"])){
	if(!isset($_SESSION["VAR_profil"]["email"])){
		exit;
	}

	// Connect to database
	$host = 'db';
	$user = 'MYSQL_USER';
	$pass = 'MYSQL_ROOT_PASSWORD';
	$database = 'usersdata';

	$idAddress = $_GET["i"];
	if(isset($_GET["n"]) && $_GET["n"] != ""){
		$name = $_GET["n"];
	} else{
		$name = null;
	}

	if($name == ""){

	}

	$link = mysqli_connect($host,'root', $pass, $database);

	// Check connection
	if (!$link) {
		die("Erreur de connexion à la base de données : " . mysqli_connect_error());
	}

	if($name == null){
		$query = "SELECT idParking FROM Parking WHERE idAddress = ".$idAddress." AND name = NULL";
	} else{
		$query = "SELECT idParking FROM Parking WHERE idAddress = ".$idAddress." AND name = \"".$name."\"";
	}
	
	if($resultSQL = mysqli_query($link,$query)){
		if(mysqli_num_rows($resultSQL)>0){
			// It's a duplicate, the value already exists
			if($tab = mysqli_fetch_assoc($resultSQL)){
				echo $tab['idParking'];
			}
			mysqli_close($link);
			exit;
		}
	} else{
		exit;
	}
	
	$query = "INSERT INTO Parking VALUES(NULL,".$idAddress.",\"".$name."\")";

	if($name == null){
		$query = "INSERT INTO Parking VALUES(NULL,".$idAddress.", NULL)";
	} else{
		$query = "INSERT INTO Parking VALUES(NULL,".$idAddress.",\"".$name."\")";
	}

	if($resultSQL = mysqli_query($link,$query)) {
		echo mysqli_insert_id($link);
		mysqli_close($link);
	}
}
?>