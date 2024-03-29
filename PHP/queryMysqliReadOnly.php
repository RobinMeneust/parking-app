<?php session_start();

class ValueWithDate {
	public $year;
	public $month;
	public $value;

	function __construct($year, $month, $value)  {
		$this->year = $year;
		$this->month = $month;
		$this->value = $value;
	}
}

class Parking {
	public $expenses;
	public $name;
	public $houseNumber;
	public $street;
	public $city;
	public $country;
	public $postalCode;

	function __construct($e, $n, $h, $s, $ci, $co, $p) {
		$this->expenses = $e;
		$this->name = $n;
		$this->houseNumber = $h;
		$this->street = $s;
		$this->city = $ci;
		$this->country = $co;
		$this->postalCode = $p;
	}
}

class ParkingFull extends Parking {
	public $dateVisited;

	function __construct($d, $e, $n, $h, $s, $ci, $co, $p) {
		parent::__construct($e, $n, $h, $s, $ci, $co, $p);
		$this->dateVisited = $d;
	}
}

class ParkingGroupBy extends Parking {
	public $nbVisits;

	function __construct($nbVisits, $e, $n, $h, $s, $ci, $co, $p) {
		parent::__construct($e, $n, $h, $s, $ci, $co, $p);
		$this->nbVisits = $nbVisits;
	}
}

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["data"])){
	if(!isset($_SESSION["VAR_profil"]["email"])){
		exit;
	}

	// Connect to database
	$host = 'db';
	$user = 'MYSQL_USER';
	$pass = 'MYSQL_ROOT_PASSWORD';
	$database = 'usersdata';

	$link = mysqli_connect($host,'root', $pass, $database);

	$data=$_GET["data"];

	// Check connection
	if (!$link) {
		die("Erreur de connexion à la base de données : " . mysqli_connect_error());
	}

	$result = array();

	if(isset($_GET["y"])){
		if($data == "expenses"){
			$query = "SELECT MONTH(dateVisited) AS m, SUM(expenses) AS n FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND YEAR(dateVisited) = \"".$_GET["y"]."\" GROUP BY MONTH(dateVisited);";
		} else if($data == "visits"){
			$query = "SELECT MONTH(dateVisited) AS m, COUNT(dateVisited) AS n FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND YEAR(dateVisited) = \"".$_GET["y"]."\" GROUP BY MONTH(dateVisited);";
		} else {
			exit;
		}

		if($resultSQL = mysqli_query($link,$query)) {			
			$result = array();
			while($row = mysqli_fetch_assoc($resultSQL)){
				array_push($result, new ValueWithDate(intval($_GET["y"]), intval($row["m"]), floatval(number_format((float) $row["n"], 2, '.', ''))));
			}
			echo json_encode($result);
		}
	} else if($data == "idUser"){
		$query = "SELECT idUser FROM Users WHERE email = \"".$_SESSION['VAR_profil']['email']."\";";
		if($resultSQL = mysqli_query($link,$query)) {
			if($row = mysqli_fetch_assoc($resultSQL)) {
				echo $row['idUser'];
			}
		}
	} else if(isset($_GET["start"]) && isset($_GET["end"])) {
		if(!isset($_SESSION["VAR_profil"]["email"])) {
			exit;
		}

		$start=$_GET["start"];
		$end=$_GET["end"];

		// Check connection
		if (!$link) {
			die("Erreur de connexion à la base de données : " . mysqli_connect_error());
		}

		if($data == "expensesProfile") {
			$query = "SELECT SUM(expenses) AS total FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE);";

			if($resultSQL = mysqli_query($link,$query)) {
				if($row = mysqli_fetch_assoc($resultSQL)) {
					echo $row["total"];
				}
			}

		} else if($data == "favoriteProfile") {
			$query = "SELECT p.name AS favpark FROM ParkingVisite v JOIN Users u ON v.idUser = u.idUser JOIN Parking p  ON p.idParking = v.idParking WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE) GROUP BY p.idParking ORDER BY COUNT(p.idParking) DESC LIMIT 1;";

			if($resultSQL = mysqli_query($link,$query)) {
				if($row = mysqli_fetch_assoc($resultSQL)) {
					echo $row["favpark"];
				}
			}

		} else if($data == "visitedProfile") {
			$query = "SELECT COUNT(idVisite) AS visPark FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN CAST(\"".$start."\" AS DATE) AND CAST(\"".$end."\" AS DATE);";
			
			if($resultSQL = mysqli_query($link,$query)) {
				if($row = mysqli_fetch_assoc($resultSQL)) {
					echo $row["visPark"];
				}
			}
		} else if($data == "allExpensesByMonthMean") {
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
					array_push($result, new ValueWithDate(intval($row["yearVisited"]), intval($row["monthVisited"]), floatval(number_format((float) $row["expenses"], 2, '.', ''))));
				}
				echo json_encode($result);
			}
		}
	} else if($data == "allVisitsFull") {
		$query = "SELECT dateVisited AS d, expenses AS e, name AS n, houseNumber AS h, street AS s, city AS ci, country AS co, postalCode AS p FROM ParkingVisite v JOIN Users u ON v.idUser = u.idUser JOIN Parking p ON p.idParking = v.idParking JOIN Addresses a ON a.idAddress = p.idAddress WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" ORDER BY dateVisited DESC;";

		if($resultSQL = mysqli_query($link,$query)) {
			$result = array();
			while($row = mysqli_fetch_assoc($resultSQL)){
				array_push($result, new ParkingFull($row["d"], floatval(number_format((float) $row["e"], 2, '.', '')), $row["n"], $row["h"], $row["s"], $row["ci"], $row["co"], $row["p"]));
			}
			echo json_encode($result);
		}
	} else if($data == "allVisits") {
		$query = "SELECT COUNT(p.idParking) AS nb, SUM(expenses) AS e, name AS n, houseNumber AS h, street AS s, city AS ci, country AS co, postalCode AS p FROM ParkingVisite v JOIN Users u ON v.idUser = u.idUser JOIN Parking p ON p.idParking = v.idParking JOIN Addresses a ON a.idAddress = p.idAddress WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" GROUP BY p.idParking";

		if($resultSQL = mysqli_query($link,$query)) {
			$result = array();
			while($row = mysqli_fetch_assoc($resultSQL)){
				array_push($result, new ParkingGroupBy($row["nb"], floatval(number_format((float) $row["e"], 2, '.', '')), $row["n"], $row["h"], $row["s"], $row["ci"], $row["co"], $row["p"]));
			}
			echo json_encode($result);
		}
	}
	exit;
}

?>