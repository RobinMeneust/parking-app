<?php session_start(); 

//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Inscription</title>
    
        <link rel="stylesheet" type="text/css" href="CSS/Inscription_Connexion.css">
        <?php include_once("head.php"); ?>
    </head>

    <body>
    <!-- include header there : <?php //include('Header.php'); ?> -->
        <?php include_once("Header.php"); ?>

        <section>

            <div class="Form-container">

                <div class="Form-card">
                
                    <div id="Login" class="form-header active">
                        <h1>CONNEXION</h1>
                    </div> 

                    <div id="Register" class="form-header">
                        <h1>INSCRIPTION</h1>
                    </div>

                </div> 

                <div id="IDForm-Body"class="Form-body">

                    

                    <form id="FormLogin" method="POST" action="./PHP/verification_Connexion.php" >

                        <h2> Connectez-vous <br />sur <br/>Park'o Top</h2>

                        <?php 
                            if (isset($_GET["message"]) && !empty($_GET["message"]) ) {
                                $error_msg = htmlspecialchars($_GET["message"]);
                                include("PHP/error_msg.php");
                            }
                        ?>

                        <div> 

                            <div class="inputBox">
                                <span>Mail</span><br /> 
                                <input type="text" class="inputField" name="email" placeholder="Entrez votre mail" value="<?= isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] != "error" ? $_SESSION["VAR_profil"]["email"] : "";?>">
                            </div>
                            <div class="inputBox">
                                <span>Mot de passe</span><br />
                                <input type="password" class="inputField" name="password" placeholder="Entrez votre mot de passe">
                            </div>
                            <div class="inputBox">
                                <input type="submit" name="submit" class="button" value="Valider">
                            </div>
                        
                        </div>

                    </form>

                    

                    <form id="FormRegister" class="ToggleForm" method="POST" action="./PHP/verification_Inscription.php">

                        <h2> BIENVENUE SUR Park'o Top </h2>

                        <?php 
                            if (isset($_GET["message"]) && !empty($_GET["message"]) ) {
                                $error_msg = htmlspecialchars($_GET["message"]);
                                include("PHP/error_msg.php");
                            }
                        ?>

                        <div class="inputBox">
                            <label>Nom</label>
                            <input type="text" name="nom" class="inputField <?= isset($_SESSION["VAR_profil"]["nom"]) && $_SESSION["VAR_profil"]["nom"] != "error" ? "input_bon": "input_mauvais";?>"  name="nom"  id="nom" max="29" placeholder="Nom" value="<?= isset($_SESSION["VAR_profil"]["nom"]) && $_SESSION["VAR_profil"]["nom"] != "error" ? $_SESSION["VAR_profil"]["nom"] : "";?>">
                            <?php if(isset($_SESSION["VAR_profil"]["nom"]) && $_SESSION["VAR_profil"]["nom"] == "error") { ?>
                                        <p class="error-msg">Veuillez retournez un nom svp</p>
                            <?php }?>
                        </div>  

                        <div class="inputBox">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="inputField <?= isset($_SESSION["VAR_profil"]["prenom"]) && $_SESSION["VAR_profil"]["prenom"] != "error" ? "input_bon": "input_mauvais";?>" id="prenom" max="29" placeholder="Prenom" value="<?= isset($_SESSION["VAR_profil"]["prenom"]) && $_SESSION["VAR_profil"]["prenom"] != "error" ? $_SESSION["VAR_profil"]["prenom"] : "";?>" >
                            <?php if(isset($_SESSION["VAR_profil"]["prenom"]) && $_SESSION["VAR_profil"]["prenom"] == "error") { ?>
                                        <p class="error-msg">Veuillez retournez un prenom svp</p>
                            <?php }?>
                        </div>  

                        <div class="inputBox">
                            <label>Adresse Email</label>
                            <input type="text" name="email" class="inputField <?= isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] != "error" ? "input_bon": "input_mauvais";?>" id="email" size="30" max="29" placeholder="Adresse électronique" value="<?= isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] != "error" ? $_SESSION["VAR_profil"]["email"] : "";?>" >
                            <?php if(isset($_SESSION["VAR_profil"]["email"]) && $_SESSION["VAR_profil"]["email"] == "error") { ?>
                                        <p class="error-msg">Veuillez retournez un email svp</p>
                            <?php }?> 
                        </div> 

                        

                        <div class="inputBox">
                            <label>Mot de passe</label>
                            <input type="password"  name="password" class="inputField <?= isset($_SESSION["VAR_profil"]["password"]) && $_SESSION["VAR_profil"]["password"] != "error" ? "input_bon": "input_mauvais";?>" id="password" max="29" placeholder="Mot de passe" value="<?= isset($_SESSION["VAR_profil"]["password"]) && $_SESSION["VAR_profil"]["password"] != "error" ? $_SESSION["VAR_profil"]["password"] : "";?>" >
                            <?php if(isset($_SESSION["VAR_profil"]["password"]) && $_SESSION["VAR_profil"]["password"] == "error") { ?>
                                        <p class="error-msg">Veuillez retournez un password svp</p>
                            <?php }?>
                        </div>  

                        <div class="inputBox">
                            <label>Adresse</label>
                            <textarea name="adresse" class="textarea <?= isset($_SESSION["VAR_profil"]["adresse"]) && $_SESSION["VAR_profil"]["adresse"] != "error" ? "input_bon": "input_mauvais";?>"  id="adresse" rows="5"  placeholder="95 rue de monmorency Montigny"><?= isset($_SESSION["VAR_profil"]["adresse"]) && $_SESSION["VAR_profil"]["adresse"] != "error" ? $_SESSION["VAR_profil"]["adresse"] : "";?></textarea>
                            <?php if(isset($_SESSION["VAR_profil"]["adresse"]) && $_SESSION["VAR_profil"]["adresse"] == "error") { ?>
                                        <p class="error-msg">Veuillez retournez un adresse svp</p>
                            <?php }?>
                        </div> 

                        <div class="inputBox">
                            <label>Code postal</label>
                            <input type="text" name="code_postal" class="inputField <?= isset($_SESSION["VAR_profil"]["code_postal"]) && $_SESSION["VAR_profil"]["code_postal"] != "error" ? "input_bon": "input_mauvais";?>" id="code_postal" max="29" placeholder="Code postal" value="<?= isset($_SESSION["VAR_profil"]["code_postal"]) && $_SESSION["VAR_profil"]["code_postal"] != "error" ? $_SESSION["VAR_profil"]["code_postal"] : "";?>">
                            <?php if(isset($_SESSION["VAR_profil"]["code_postal"]) && $_SESSION["VAR_profil"]["code_postal"] == "error") { ?>
                                        <p class="error-msg">Veuillez retournez un metier svp</p>
                            <?php }?>  
                        </div> 

                        <div class="inputBox terms">
                            <label class="check">
                                <input type="checkbox" name="valid_conditions">
                                <span class="checkmark"></span>
                            </label>
                            <p>Agreed to terms and conditions</p>
                        </div> 

                        <div class="inputBox">
                            <input type="submit" name="submit" value="Register" class="btn">
                        </div>
                        

                    </form>
                </div>



            </div>

        </section>

        <script>
    
            const FormLogin= _('FormLogin');
            const FormRegister= _('FormRegister');
            const Login= _('Login');
            const Register= _('Register');
            const FormBody= _('IDForm-Body');
            

            function urlGet(parameterName) {
                var result = null,
                    tmp = [];
                    location.search.substr(1).split("&").forEach(function (item) {
                    tmp = item.split("=");
                    if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
                    });
                return result;
            }
            const form=urlGet("form")
            
            Login.addEventListener('click', () => {
                Register.classList.remove('active');
                Login.classList.add('active');
                if(FormLogin.classList.contains('ToggleForm')){
                    FormBody.style.transform = 'translate(0%)';
                    FormBody.style.transition = 'transform 0.5s';
                    FormLogin.classList.remove('ToggleForm');
                    FormRegister.classList.add('ToggleForm');
                }
            })

            Register.addEventListener('click', () => {
                Login.classList.remove('active');
                Register.classList.add('active');
                if(FormRegister.classList.contains('ToggleForm')){
                    FormBody.style.transform = 'translate(-100%)';
                    FormBody.style.transition = 'transform 0.5s';
                    FormRegister.classList.remove('ToggleForm');
                    FormLogin.classList.add('ToggleForm');
                }
            })

            if(form!=null && form == "Register") {
                Register.click();
            }
        



            console.log("hello");
            console.log(FormBody);
            console.log(urlGet('form'))

            function _(e) {
                return document.getElementById(e);
            }
        </script>
        
        <?php include_once("Footer.php"); ?>
    </body>

</html>