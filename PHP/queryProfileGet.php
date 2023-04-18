<?php session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["start"]) && isset($_GET["end"]) && isset($_GET["data"])) {
    
    if(!isset($_SESSION["VAR_profil"]["email"])) {
		exit;
	}

    $start=$_GET["start"];
    $end=$_GET["end"];
    $data=$_GET["data"];

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

	if($data == "expensesProfile") {
        $query = "SELECT SUM(expenses) AS total FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE);";

        if($resultSQL = mysqli_query($link,$query)) {
            $row = mysqli_fetch_assoc($resultSQL);
            echo $row["total"];
        }

    } else if($data == "favoriteProfile") {
        $query = "SELECT p.name AS favpark FROM ParkingVisite v JOIN Users u ON v.idUser = u.idUser JOIN Parking p  ON p.idParking = v.idParking WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE) GROUP BY p.idParking ORDER BY COUNT(p.idParking) DESC LIMIT 1;";

        if($resultSQL = mysqli_query($link,$query)) {
            $row = mysqli_fetch_assoc($resultSQL);
            echo $row["favpark"];
        }

    } else if ($data == "visitedProfile") {
        $query = "SELECT COUNT(idVisite) AS visPark FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE);";
        
        if($resultSQL = mysqli_query($link,$query)) {
            $row = mysqli_fetch_assoc($resultSQL);
            echo $row["visPark"];
        }

    } else {
        exit;
    }

    
}

?>