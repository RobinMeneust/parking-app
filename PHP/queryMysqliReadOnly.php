<?php session_start();

class valueWithDate {
	public $year;
	public $month;
	public $value;

	function __construct($year, $month, $value)  {
		$this->year = $year;
		$this->month = $month;
		$this->value = $value;
	}
}

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["d"])){
	if(!isset($_SESSION["VAR_profil"]["email"])){
		exit;
	}

	// Connect to database
	$host = 'db';
	$user = 'MYSQL_USER';
	$pass = 'MYSQL_ROOT_PASSWORD';
	$database = 'usersdata';

	$link = mysqli_connect($host,'root', $pass, $database);

	// Check connection
	if (!$link) {
		die("Erreur de connexion à la base de données : " . mysqli_connect_error());
	}

	$result = array();

	if(isset($_GET["y"])){
		if($_GET['d'] == "expenses"){
			$query = "SELECT MONTH(dateVisited) AS m, SUM(expenses) AS n FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND YEAR(dateVisited) = \"".$_GET["y"]."\" GROUP BY MONTH(dateVisited);";
		} else if($_GET['d'] == "visits"){
			$query = "SELECT MONTH(dateVisited) AS m, COUNT(dateVisited) AS n FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND YEAR(dateVisited) = \"".$_GET["y"]."\" GROUP BY MONTH(dateVisited);";
		} else {
			exit;
		}

		if($resultSQL = mysqli_query($link,$query)) {			
			$result = array();
			while($row = mysqli_fetch_assoc($resultSQL)){
				array_push($result, new valueWithDate(intval($_GET["y"]), intval($row["m"]), floatval(number_format((float) $row["n"], 2, '.', ''))));
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

	if(isset($_GET["start"]) && isset($_GET["end"])) {
		
		if(!isset($_SESSION["VAR_profil"]["email"])) {
			exit;
		}

		$start=$_GET["start"];
		$end=$_GET["end"];
		$data=$_GET["data"];

		// Connect to database
		$host = 'db';
		$user = 'MYSQL_USER';
		$pass = 'MYSQL_ROOT_PASSWORD';
		$database = 'usersdata';

		$link = mysqli_connect($host,'root', $pass, $database);

		// Check connection
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

		} else if($data == "visitedProfile") {
			$query = "SELECT COUNT(idVisite) AS visPark FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE);";
			
			if($resultSQL = mysqli_query($link,$query)) {
				$row = mysqli_fetch_assoc($resultSQL);
				echo $row["visPark"];
			}
		} else if($data == "firstDateWithData") {
			$query = "SELECT YEAR(dateVisited) AS yearVisited, MONTH(dateVisited) AS monthVisited FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" ORDER BY YEAR(dateVisited), MONTH(dateVisited) ASC LIMIT 1;";
			
			if($resultSQL = mysqli_query($link,$query)) {
				$row = mysqli_fetch_assoc($resultSQL);		
				return $row;
			}
		}else if($data == "allExpensesByMonthMean") {
			$query = "SELECT MONTH(dateVisited) as monthVisited, AVG(expenses) AS expenses FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" GROUP BY MONTH(dateVisited);";
			$result = array_fill(1, 12, 0.0);
			if($resultSQL = mysqli_query($link,$query)) {
				while($row = mysqli_fetch_assoc($resultSQL)){
					$result[$row["monthVisited"]] = floatval(number_format((float) $row["expenses"], 2, '.', '')); // convert string to float with a 0.01 precision
					
				}
				
				if(!empty($result)){
					echo json_encode($result);
				}
			}
		} else if($data == "allExpensesByMonthInRange") {
			$query = "SELECT YEAR(dateVisited) AS yearVisited, MONTH(dateVisited) AS monthVisited, COALESCE(SUM(expenses),0) AS expenses FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN STR_TO_DATE(\"".$start."\", \"%Y-%m-%d\") AND STR_TO_DATE(\"".$end."\", \"%Y-%m-%d\") GROUP BY YEAR(dateVisited), MONTH(dateVisited);";
			if($resultSQL = mysqli_query($link,$query)) {			
				$result = array();
				while($row = mysqli_fetch_assoc($resultSQL)){
					array_push($result, new valueWithDate(intval($row["yearVisited"]), intval($row["monthVisited"]), floatval(number_format((float) $row["expenses"], 2, '.', ''))));
				}
				echo json_encode($result);
			}
		}else {
			exit;
		}
	}
}

?>