<?php
session_start();
if(!isset($_SESSION["currentParking"])){
    header('location:../Inscription_Connexion.php?message=Veuillez vous connecter');
}

echo json_encode($_SESSION["currentParking"]);
exit;
?>