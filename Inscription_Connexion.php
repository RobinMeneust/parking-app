<?php session_start(); 

//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inscription</title>
    
        <link rel="stylesheet" type="text/css" href="assets/css/Inscription_Connexion.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
        <?php include_once("head.php"); ?>
    </head>

    <body>
    <!-- include header there : <?php //include('Header.php'); ?> -->
        <?php include_once("Header.php"); ?>


        <?php 
            if (isset($_GET["message"]) && !empty($_GET["message"]) ) {
                $error_msg = htmlspecialchars($_GET["message"]);
                include("PHP/error_msg.php");
            }
        ?>
        <div class="CONN-INSC">
            <div class="container_INS_CONN" id="container">
                <div class="form-container sign-up-container"> 
                    <form action="./PHP/verification_Inscription.php" method="POST" class="form"> 
                        <h1>Créer un compte</h1>
                        

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

                        <div class="input-container">
                        <textarea name="adresse" class="text-input" rows="1" cols="26" placeholder=" Adresse" required><?= isset($_SESSION["VAR_profil"]["adresse"]) && $_SESSION["VAR_profil"]["adresse"] != "error" ? $_SESSION["VAR_profil"]["adresse"] : "" ;?></textarea>
                            <label for="adresse" class="label">Adresse</label>
                        </div>


                        <div class="input-container">
                            <input id="codePostal" type="text" name="postalCode" class="text-input" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["postalCode"]) && $_SESSION["VAR_profil"]["postalCode"] != "error" ? $_SESSION["VAR_profil"]["postalCode"] : "";?>" required >
                            <label  id="label_CodePostal" for="code_postal" class="label">Code Postal</label>
                        </div>

                        <input type="checkbox" name="valid_conditions" required>
                        <span class="checkmark">Agreed to terms and conditions</span>
                        <input type="submit"  name="submit" value="Register" class="btn">
                    </form>
                </div>

                <div class="form-container sign-in-container">
                    <form action="./PHP/verification_Connexion.php" method="POST" class="form"> 
                        <h1>Se connecter</h1>
                        <div class="social-container">
                            <a href="#" class="social"><i class="fa-brands fa-google-plus-g fa-bounce"></i></a>
                            <a href="#" class="social"><i class="fa-brands fa-facebook-f fa-bounce"></i></a>
                        </div>
                        <span>Ou utilisez votre compte pour vous inscrire</span>
                        <div class="input-container">
                            <input type="email" name="email" class="text-input"id="mail" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] != "error" ? $_SESSION["VAR_profil"]["email"] : "";?>" required >
                            <label for="email" class="label">adresse-mail</label>
                        </div>
                        <div class="input-container">
                            <input type="password" name="passwd" class="text-input"id="mdp" placeholder="" value="<?= isset($_SESSION["VAR_profil"]["passwd"]) && $_SESSION["VAR_profil"]["passwd"] != "error" ? $_SESSION["VAR_profil"]["passwd"] : "";?>"  required>
                            <label for="passwd" class="label">Mot de passe</label>
                        </div>
                        <input type="submit"  name="submit" value="Se connecter" class="btn">
                    </form>
                </div>

                <div class="overlay-container">
                    <div class="overlay">
                        <div class="overlay-panel overlay-left">
                            <h1>Bienvenue sur Park'o Top</h1>
                            <button class="press btn" id="signIn">Se connecter</button>
                        </div>

                        <div class="overlay-panel overlay-right">
                            <h1>Bienvenue sur Park'o Top</h1>
                            <p> Ajouter une image standard</p>
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