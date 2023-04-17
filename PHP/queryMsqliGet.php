<?php session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["d"])){

	if(!isset($_SESSION["VAR_profil"]["email"])){
		exit;
	}

	// Connexion à la base de données MySQL
	$host = 'db';
	$user = 'MYSQL_USER';
	$pass = 'MYSQL_ROOT_PASSWORD';
	$database = 'usersdata';

	$link = mysqli_connect($host,'root', $pass, $database);

	// Vérification de la connexion
	if (!$link) {
		die("Erreur de connexion à la base de données : " . mysqli_connect_error());
	}

	$result = array();

	if(isset($_GET["y"])){
		if($_GET['d'] == "expenses"){
			$query = "SELECT MONTH(dateVisited) AS d, SUM(expenses) AS n FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND YEAR(dateVisited) = \"".$_GET["y"]."\" GROUP BY MONTH(dateVisited);";
		} else if($_GET['d'] == "visits"){
			$query = "SELECT MONTH(dateVisited) AS d, COUNT(dateVisited) AS n FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND YEAR(dateVisited) = \"".$_GET["y"]."\" GROUP BY MONTH(dateVisited);";
		} else {
			exit;
		}

		if($resultSQL = mysqli_query($link,$query)) {
			while($row = mysqli_fetch_assoc($resultSQL)){
				array_push($result, $row);
			}
			echo json_encode($result);
		}
	} else if($_GET['d'] == "idUser"){
		$query = "SELECT idUser FROM Users WHERE email = \"".$_SESSION['VAR_profil']['email']."\";";
		if($resultSQL = mysqli_query($link,$query)) {
			$row = mysqli_fetch_assoc($resultSQL);
			echo $row['idUser'];
		}
	}
}

?>