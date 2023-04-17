<?php session_start(); 

if(!isset($_SESSION['VAR_profil'])){
	header('location:../registerLogin.php?message=Veuillez vous connecter');
}

?>

<!DOCTYPE html>
<html lang="fr"> 
    <head>
        <title>Profil</title>
        <?php include_once("head.php"); ?>
        <script src="JS/userProfile.js"></script>
    </head>

    <body class="light" onload="refreshDate()">
        <?php include_once("Header.php"); ?>

        <div class="content">
            <div class="navProfile">
                <div class="navProfileTime">
                    <input id="yearProfile" name="yearProfile" style="text-align:center;" class="menuButton rectangular" type="number" value="2023" min="1900" max="2023">
                    <button id="refreshDateProfile" style="text-align:center;" class="menuButton rectangular">Valider</button>
                </div>
                <h1 id="profileTitle">HISTORIQUE</h1>
                <form action="PHP/signOut.php" methode="POST">
                    <input type="submit" name="deconnecte" value="Se deconnecter" id="profileLink"/> 
                </form>
            </div>
            
            <table id="profileTable">
                <tr>
                    <th>VOUS AVEZ DEPENSE AU TOTAL</th>
                    <th>VOTRE PARKING FAVORI</th>
                    <th>VOUS VOUS ETES GARE</th>
                </tr>
                <tr id="profileTableRowData">
                    <td id="expensesProfileTable"></td>
                    <td id="favoriteParkingProfileTable"></td>
                    <td id="visitedProfileTable"></td>
                </tr>
                <tr id="profileTableLink">
                    <td><a href="userGraphs.php">DETAIL GRAPHIQUE<a></td>
                    <td></td>
                    <td><a href="userGraphs.php">DETAIL HISTOGRAMME<a></td>
                </tr>
			</table>

            <div class="footerProfile">
                <!--Mettre le lien de la page-->
                <a href="#">HISTORIQUE DETAILLE</a>
            </div>
        </div>
        
        <?php include_once("Footer.php"); ?>
    </body>
</html>