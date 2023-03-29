<?php session_start(); 

//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html> 
    <head>
        <meta charset="UTF-8">
        <title>Inscription</title>
    
        <link rel="stylesheet" type="text/css" href="assets/css/Inscription_Connexion.css">
        <?php include_once("head.php"); ?>
    </head>

    <body>
        <?php include_once("Header.php"); ?>

        <form action="PHP/deconnexion.php" methode="POST">
            <input  type="submit" name="deconnecte" value="Se deconnecter"  /> 
        </form>






        <?php include_once("Footer.php"); ?>
    </body>

</html>