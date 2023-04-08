<?php
    session_start();
    $VAR_profil = Array();

    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){

        // Connexion à la base de données MySQL
		$host = 'db';
		$user = 'MYSQL_USER';
		$pass = 'MYSQL_ROOT_PASSWORD';
		$database = 'usersdata';
		$link = mysqli_connect($host,'root', $pass, $database);
		
        // Vérification de la connexion
        if (!$link) {
            die("Erreur de connexion à la base de données : " . mysqli_connect_error());
        }

        if(isset($_POST["nom"]) && !empty($_POST["nom"]) && preg_match("/^[A-Za-z]*$/", $_POST["nom"])) {
            $_SESSION["nom"] = $_POST["nom"];
            $VAR_profil["nom"] = $_SESSION["nom"];
            
        } else {
            $_SESSION["nom"] = "error"; 
            $VAR_profil["nom"] = $_SESSION["nom"];
        }
        
        if(isset($_POST["prenom"]) && !empty($_POST["prenom"]) && preg_match("/^[a-zA-Z\s]*$/", $_POST["prenom"])) { 
            $_SESSION["prenom"] = $_POST["prenom"];
            $VAR_profil["prenom"] = $_SESSION["prenom"];
        } else {
            $_SESSION["prenom"] = "error"; 
            $VAR_profil["prenom"] = $_SESSION["prenom"];
        }
        
        if(isset($_POST["password"]) && !empty($_POST["password"]) ) {
            $_SESSION["password"] = password_hash($_POST["password"], PASSWORD_BCRYPT);
            $VAR_profil["password"] = $_SESSION["password"];
            
        } else {
            $_SESSION["password"] = "error"; 
            $VAR_profil["password"] = $_SESSION["password"];
        }
        
        if(isset($_POST["email"]) && !empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["email"] = $_POST["email"];
            $VAR_profil["email"] = $_SESSION["email"];
            
        } else {
            $_SESSION["email"] = "error"; 
            $VAR_profil["email"] = $_SESSION["email"];
        }
        
        if(isset($_POST["adresse"]) && !empty($_POST["adresse"])) {
            $_SESSION["adresse"] = $_POST["adresse"];
            $VAR_profil["adresse"] = $_SESSION["adresse"];
            
        } else {
            $_SESSION["adresse"] = "error"; 
            $VAR_profil["adresse"] = $_SESSION["adresse"];
        }
        
        if(isset($_POST["code_postal"]) && !empty($_POST["code_postal"]) && preg_match("/^[0-9][0-9]*$/", $_POST["code_postal"])) {
            $_SESSION["code_postal"] = $_POST["code_postal"];
            $VAR_profil["code_postal"] = $_SESSION["code_postal"];
            
        } else {
            $_SESSION["code_postal"] = "error"; 
            $VAR_profil["code_postal"] = $_SESSION["code_postal"];
        }
        
        if(isset($_POST["valid_conditions"]) && !empty($_POST["valid_conditions"]) ) {
            $_SESSION["valid_conditions"] = $_POST["valid_conditions"];
            $VAR_profil["valid_conditions"] = $_SESSION["valid_conditions"];
            
        } else {
            $_SESSION["valid_conditions"] = "error"; 
            $VAR_profil["valid_conditions"] = $_SESSION["valid_conditions"];
        }

        $VAR_profil["connecte"] = 0;
        
        unset($_SESSION["prenom"]);
        unset($_SESSION["nom"]);
        unset($_SESSION["password"]);
        unset($_SESSION["email"]);
        unset($_SESSION["adresse"]);
        unset($_SESSION["code_postal"]);
        unset($_SESSION["valid_conditions"]);

        $_SESSION["VAR_profil"]= $VAR_profil;

        if ( $_SESSION["VAR_profil"]["prenom"] == "error" || $_SESSION["VAR_profil"]["nom"] == "error" ||   $_SESSION["VAR_profil"]["password"] == "error" || $_SESSION["VAR_profil"]["email"]  == "error" || $_SESSION["VAR_profil"]["adresse"]  == "error"|| $_SESSION["VAR_profil"]["code_postal"] =="error" || $_SESSION["VAR_profil"]["valid_conditions"] =="error" ) {
        
            header('location:../Inscription_Connexion.php?message=A refaire&form=Register');
            exit;
        
        
        }
        
        // verification mail
        if($resultSQL = mysqli_query($link,"SELECT mail FROM users;")) {
            while($tab = mysqli_fetch_assoc($resultSQL)){
                if($_SESSION["VAR_profil"]["email"] == $tab["mail"]) {
                mysqli_free_result($resultSQL);
                header('location:../Inscription_Connexion.php?message=Mail déjà utilisé&form=Register');
                exit;
                }
            }
            mysqli_free_result($resultSQL);
        } 
        // Ajout donnée dans la base donnée

        $sql = "INSERT INTO users (nom, prenom, mdp, mail, adresse, code_postal, agreement) VALUES ('".$_SESSION['VAR_profil']['nom']."', '".$_SESSION['VAR_profil']['prenom']."', '".$_SESSION['VAR_profil']['password']."', '".$_SESSION['VAR_profil']['email']."',  '".$_SESSION['VAR_profil']['adresse']."', '".$_SESSION['VAR_profil']['code_postal']."', '".$_SESSION['VAR_profil']['valid_conditions']."')";

        if(mysqli_query($link, $sql)) {
            $_SESSION["VAR_profil"]["connecte"]= 1;
            mysqli_close($link);
            header('location:../index.php?message=Connecté');
            exit;
        } else {
            echo 'Error Occured';
        }
        
        
    } echo 'not connected';
?>










