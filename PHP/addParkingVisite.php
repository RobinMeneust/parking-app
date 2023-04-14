<?php
session_start();

// idUser, idParking, dateVisited, expenses
if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["u"]) && isset($_GET["p"])  && isset($_GET["d"])  && isset($_GET["e"])){
	if(!isset($_SESSION["VAR_profil"]["email"])){
		exit;
	}

	// Connect to database
	$host = 'db';
	$user = 'MYSQL_USER';
	$pass = 'MYSQL_ROOT_PASSWORD';
	$database = 'usersdata';

	$idUser = $_GET["u"];
	$idParking = $_GET["p"];
	$dateVisited = $_GET["d"];
	$expenses = $_GET["e"];

	$link = mysqli_connect($host,'root', $pass, $database);

	// Check connection
	if (!$link) {
		die("Erreur de connexion à la base de données : " . mysqli_connect_error());
	}
	
	$query = "INSERT INTO ParkingVisite VALUES(NULL,".$idUser.",".$idParking.",'".$dateVisited."',".$expenses.");";

	if($resultSQL = mysqli_query($link,$query)) {
		mysqli_close($link);
	} else{
		echo mysqli_error($link);
	}
} else {
	echo "incorrect PHP params";
}
?>