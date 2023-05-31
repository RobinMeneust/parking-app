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

        <div class="contentProfil">
            <h1 id="profileTitle">HISTORIQUE</h1>
            <div class="navProfile">
                <div id="navProfileTime">

                    <div>
                        <label for=date>Du</label>
                        <input type="date" id="Start_date" class="date_input" name="Start_date" oninput="refreshDate()" min="2010-01-01" max="2030-12-31" require>

                        <label for=date>au</label>
                        <input type="date" id="End_date" class="date_input" name="End_date" oninput="refreshDate()" min="2010-01-01" max="2030-12-31" require>
                    </div>
                        

                    <p id="date_error_message" style="display: none;"> La date de fin est inférieur à la date de début</p>
                    
                    <script>
                        const Start_input_Date = document.getElementById('Start_date');
                        const End_input_Date = document.getElementById('End_date');
                        const date_input = document.getElementsByClassName('date_input')
                        const errorMessage = document.getElementById('date_error_message');

                        //console.log('start/end input date html');
                        //console.log(Start_input_Date);

                        for(let i = 0; i < date_input.length; i++){
                            date_input[i].addEventListener('change', function() {
                            const selected_Start_Date = new Date(Start_input_Date.value);
                            const selected_End_Date = new Date(End_input_Date.value);
                            //console.log('start/end input date js');

                            const date_Limit = new Date(2010, 0, 1); // janvier est le mois 0
                            //console.log(date_Limit);

                            if (selected_End_Date < selected_Start_Date) {
                                errorMessage.style.display = 'block';
                            } else if (selected_Start_Date < date_Limit) {
                                errorMessage.style.display = 'block';
                            } else {
                                errorMessage.style.display = 'none';
                            }
                            });
                        }
                        
                    </script>

                </div>
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
                    <td><a href="userGraphs.php" class="detailsButtons" style="--clr:#1e9bff"><span>DETAIL GRAPHIQUE</span><i></i></a></td>
                    <td></td>
                    <td><a href="userGraphs.php" class="detailsButtons" style="--clr:#6eff3e"><span>DETAIL HISTOGRAMME</span><i></i></a></td>
                </tr>
			</table>

            <div id="linkDetailledProfile">
                <a href="#">HISTORIQUE DETAILLE</a>
            </div>

            <div>
                <a href="predictExpenses.php" class="detailsButtons" style="--clr:#1e9bff"><span>Prédiction des dépenses</span><i></i></a></td>
            </div>
        </div>
        
        <?php include_once("Footer.php"); ?>
    </body>
</html>