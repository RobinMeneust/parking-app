<?php session_start(); 

//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inscription</title>
    
        <link rel="stylesheet" type="text/css" href="assets/css/register_login.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <?php include_once("head.php"); ?>
    </head>

    <body>
    <!-- include header there : <?php //include('Header.php'); ?> -->
        <?php include_once("Header.php"); ?>


        <?php 
            if (isset($_GET["message"]) && !empty($_GET["message"]) ) {
                $error_msg = htmlspecialchars($_GET["message"]);
                include("PHP/errorMessage.php");
            }
        ?>
        <div class="CONN-INSC">
            <div class="container_INS_CONN" id="container">
                <div class="form-container sign-up-container"> 
                    <form action="./PHP/registrationCheck.php" method="POST" class="form"> 
                        <h1 class="titleLogin">Créer un compte</h1>
                        

                        <div class="input-container">
                            <input type="text" name="lastName" class="text-input"id="name" placeholder=""  value="<?= isset($_SESSION["VAR_profil"]["lastName"]) && $_SESSION["VAR_profil"]["lastName"] != "error" ? $_SESSION["VAR_profil"]["lastName"] : "";?>" required  >
                            <label for="nom" class="label">Nom</label>
                        </div>  
                        
                        <div class="input-container">
                            <input type="text" name="firstName" class="text-input" id="prenom" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["firstName"]) && $_SESSION["VAR_profil"]["firstName"] != "error" ? $_SESSION["VAR_profil"]["firstName"] : "";?>" required >
                            <label for="prenom" class="label">Prenom</label>
                        </div>

                        <div class="input-container">
                            <input type="email" name="email" class="text-input" id="mail" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] != "error" ? $_SESSION["VAR_profil"]["email"] : "";?>" required >
                            <label for="mail" class="label">adresse-mail</label>
                        </div>

                        <div class="input-container">
                            <input type="password" name="passwd" class="text-input" id="mdp" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["passwd"]) && $_SESSION["VAR_profil"]["passwd"] != "error" ? $_SESSION["VAR_profil"]["passwd"] : "";?>"  required>
                            <label for="mdp" class="label">Mot de passe</label>
                        </div>

                        <input type="checkbox" name="valid_conditions" required>
                        <span class="checkmark">Agreed to terms and conditions</span>
                        <input type="submit"  name="submit" value="Register" class="btn">
                    </form>
                </div>

                <div class="form-container sign-in-container">
                    <form action="./PHP/connectionCheck.php" method="POST" class="form"> 
                        <h1 class="titleLogin">Se connecter</h1>
                        <div class="input-container">
                            <input type="email" name="email" class="text-input"id="mail" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] != "error" ? $_SESSION["VAR_profil"]["email"] : "";?>" required >
                            <label for="email" class="label">adresse-mail</label>
                        </div>
                        <div class="input-container">
                            <input type="password" name="passwd" class="text-input"id="mdp" placeholder="" value=""  required>
                            <label for="passwd" class="label">Mot de passe</label>
                        </div>
                        <input type="submit"  name="submit" value="Se connecter" class="btn">
                    </form>
                </div>

                <div class="overlay-container">
                    <div class="overlay">
                        <div class="overlay-panel overlay-left">
                            <h1>Bienvenue sur Park'o Top</h1>
                            <img class="imgLogin" src="./assets/img/logo3.png" alt=""/>
                            <button class="press btn" id="signIn">Se connecter</button>
                        </div>

                        <div class="overlay-panel overlay-right">
                            <h1>Bienvenue sur Park'o Top</h1>
                            <img class="imgLogin" src="./assets/img/logo3.png" alt=""/>
                            <button class="press btn" id="signUp" >S'inscrire</button>
                        </div>
                    </div>
                </div>
                
            </div>  

            <script>
                let signUpButton = document.getElementById("signUp")
                let signInButton = document.getElementById("signIn")
                let container = document.getElementById("container")

                signUpButton.addEventListener('click',() => {
                    container.classList.add("right-panel-active");
                })

                signInButton.addEventListener('click',() => {
                    container.classList.remove("right-panel-active");
                })
            </script>
        </div>
        
        <?php include_once("Footer.php"); ?>
    </body>

</html>