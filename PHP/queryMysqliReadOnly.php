<?php session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["start"]) && isset($_GET["end"]) && isset($_GET["data"])) {
    
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
		$query = "SELECT MONTH(dateVisited) AS monthVisited, COALESCE(SUM(expenses),0) AS expenses FROM ParkingVisite p JOIN Users u ON p.idUser = u.idUser WHERE email = \"".$_SESSION["VAR_profil"]["email"]."\" AND dateVisited BETWEEN STR_TO_DATE(\"".$start."\", \"%Y-%m-%d\") AND STR_TO_DATE(\"".$end."\", \"%Y-%m-%d\") GROUP BY MONTH(dateVisited) ORDER BY monthVisited ASC;";
        if($resultSQL = mysqli_query($link,$query)) {
			$startMonth = intval(idate("n", strtotime($start)));
			$startYear = intval(idate("y", strtotime($start)));
			$endMonth = intval(idate("n", strtotime($end)));
			$endYear = intval(idate("y", strtotime($end)));
			$sizeResult = 0;
			if($endYear == $startYear){
				$sizeResult = $endMonth - $startMonth;
			} else {
				$sizeResult += 12 * ($endYear - $startYear - 2);
				$sizeResult += $endMonth;
			}

			if($sizeResult == 0){
				exit;
			}
			$result = array_fill(0,$sizeResult, 0.0);
			$i = 0;
			while($row = mysqli_fetch_assoc($resultSQL)){
				$result[$i] = floatval(number_format((float) $row["expenses"], 2, '.', '')); // convert string to float with a 0.01 precision
				$i++;
			}
			
			if(!empty($result)){
				echo json_encode($result);
			}
        }
	}else {
        exit;
    }

    
}

?>