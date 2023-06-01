<?php session_start();

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["action"])){
	$action = $_GET["action"];
	if($action == "switch") {
		if(!isset($_SESSION["darkModeOn"])) {
			$_SESSION["darkModeOn"] = true;
			exit;
		}
		if(is_bool($_SESSION["darkModeOn"]))
			$_SESSION["darkModeOn"] = !$_SESSION["darkModeOn"];
	} else if($action == "get") {
		if(isset($_SESSION["darkModeOn"]) && $_SESSION["darkModeOn"] == true) {
			echo "dark";
		} else {
			echo "light";
		}
	}
}
exit;

?>