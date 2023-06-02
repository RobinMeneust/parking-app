<?php session_start(); 

if(!isset($_SESSION['VAR_profil'])){
	header('location:../registerLogin.php?message=Veuillez vous connecter');
}

?>

<!DOCTYPE html>
<html lang="fr"> 
    <head>
        <title>Historique</title>
        <?php include_once("head.php"); ?>
        <script src="JS/userProfile.js"></script>
    </head>

    <body onload="refreshDate()">
        <?php include_once("Header.php"); ?>

        <div id="bandeau" class="contentProfil">
            <h1 id="profileTitle">HISTORIQUE</h1>
        </div>

        <div class="contentProfil">
            <div id="navProfileTime">

                <div>
                    <label for=date>DU</label>
                    <input type="date" id="Start_date" class="date_input" name="Start_date" oninput="refreshDate()" min="2010-01-01" max="2030-12-31" require>

                    <label for=date>AU</label>
                    <input type="date" id="End_date" class="date_input" name="End_date" oninput="refreshDate()" min="2010-01-01" max="2030-12-31" require>
                </div>
                    

                <p id="date_error_message" style="display: none;"> La date de fin est inférieure à la date de début !</p>
            </div>
            
            <table id="profileTable">
                <tr>
                    <th>DEPENSES TOTALES</th>
                    <th>PARKING FAVORI</th>
                    <th>NOMBRE DE STATIONNEMENTS</th>
                </tr>
                <tr id="profileTableRowData">
                    <td id="expensesProfileTable"></td>
                    <td id="favoriteParkingProfileTable"></td>
                    <td id="visitedProfileTable"></td>
                </tr>
                <tr id="profileTableLink">
                    <td class="hideBorderCells"><a href="userGraphs.php" class="detailsButtons" style="--clr:#1e9bff"><span>DETAIL GRAPHIQUE</span><i></i></a></td>
                    <td class="hideBorderCells"><a href="predictExpenses.php" class="detailsButtons" style="--clr:#ffba42"><span>Prédiction des dépenses</span><i></i></a></td></td>
                    <td class="hideBorderCells"><a href="userGraphs.php" class="detailsButtons" style="--clr:#6eff3e"><span>DETAIL HISTOGRAMME</span><i></i></a></td>
                </tr>
			</table>

            <div id="linkDetailledProfile">
                <a href="fullHistory.php" class="detailsButtons" style="--clr:#a98dec"><span>HISTORIQUE DETAILLE</span><i></i></a>
            </div>
        </div>
        
        <?php include_once("Footer.php"); ?>
    </body>
</html>