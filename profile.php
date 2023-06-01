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

    <body onload="profileData()">
        <?php include_once("Header.php"); ?>

        <div id="bandeau" class="contentProfil">
            <h1 id="profileTitle">INFORMATIONS PERSONNELLES</h1>
        </div>

        <div class="contentProfilPage">
            <div class="contentPage">
                <div class="dataProfile">
                    <h2>Nom</h2>
                    <div id="lastNameProfile"><?php echo $_SESSION['VAR_profil']['lastName']?></div>
                </div>
                <div class="dataProfile">
                    <h2>Prénom</h2>
                    <div id="firstNameProfile"><?php echo $_SESSION['VAR_profil']['firstName']?></div>
                </div>
                <div class="dataProfile">
                    <h2>Adresse email</h2>
                    <div id="emailProfile"><?php echo $_SESSION['VAR_profil']['email']?></div>
                </div>
            </div>
        </div>        
        <?php include_once("Footer.php"); ?>
    </body>
</html>