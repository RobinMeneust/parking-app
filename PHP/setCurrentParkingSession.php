<?php session_start();

$parking = Array();
$pos = Array();
$address = Array();

$pos["lat"] = $_GET["lat"];
$pos["lng"] = $_GET["lng"];
$parking["pos"] = $pos;

$parking["name"] = $_GET["name"];

$address["houseNumber"] = $_GET["nb"];
$address["street"] = $_GET["street"];
$address["city"] = $_GET["city"];
$address["country"] = $_GET["country"];
$address["postalCode"] = $_GET["postal"];
$parking["address"] = $address;

$_SESSION["currentParking"] = $parking;

echo json_encode($_SESSION["currentParking"]);

exit;

?>