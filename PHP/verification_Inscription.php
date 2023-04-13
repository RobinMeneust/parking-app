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

        if(isset($_POST["lastName"]) && !empty($_POST["lastName"]) && preg_match("/^[A-Za-z]*$/", $_POST["lastName"])) {
            $_SESSION["lastName"] = $_POST["lastName"];
        } else {
            $_SESSION["lastName"] = "error"; 
        }
		$VAR_profil["lastName"] = $_SESSION["lastName"];
        
        if(isset($_POST["firstName"]) && !empty($_POST["firstName"]) && preg_match("/^[a-zA-Z\s]*$/", $_POST["firstName"])) { 
            $_SESSION["firstName"] = $_POST["firstName"];
        } else {
            $_SESSION["firstName"] = "error"; 
        }
		$VAR_profil["firstName"] = $_SESSION["firstName"];
        
        if(isset($_POST["passwd"]) && !empty($_POST["passwd"]) ) {
            $_SESSION["passwd"] = password_hash($_POST["passwd"], PASSWORD_BCRYPT);            
        } else {
            $_SESSION["passwd"] = "error"; 
        }
		$VAR_profil["passwd"] = $_SESSION["passwd"];
        
        if(isset($_POST["email"]) && !empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["email"] = $_POST["email"];
        } else {
            $_SESSION["email"] = "error"; 
        }
		$VAR_profil["email"] = $_SESSION["email"];
        
        if(isset($_POST["code_postal"]) && !empty($_POST["postalCode"])){
			if(preg_match("/^[0-9][0-9]*$/", $_POST["postalCode"])) {
				$_SESSION["postalCode"] = $_POST["postalCode"];
			} else {
				$_SESSION["postalCode"] = "error"; 
			}
			$VAR_profil["postalCode"] = $_SESSION["postalCode"];
        }
        
        if(!isset($_POST["valid_conditions"]) || empty($_POST["valid_conditions"]) || $_POST["valid_conditions"] != "on") {
            $_SESSION["valid_conditions"] = "error"; 
            $VAR_profil["valid_conditions"] = $_SESSION["valid_conditions"];
        }
        
        unset($_SESSION["firstName"]);
        unset($_SESSION["lastName"]);
        unset($_SESSION["passwd"]);
        unset($_SESSION["email"]);

        $_SESSION["VAR_profil"]= $VAR_profil;

        if ( $_SESSION["VAR_profil"]["firstName"] == "error" || $_SESSION["VAR_profil"]["lastName"] == "error" || $_SESSION["VAR_profil"]["passwd"] == "error" || $_SESSION["VAR_profil"]["email"]  == "error") {
            header('location:../Inscription_Connexion.php?message=Des champs sont invalides&form=Register');
            exit;
        }
        
        // verification email
        if($resultSQL = mysqli_query($link,"SELECT COUNT(email) FROM Users WHERE email = ".$_SESSION["VAR_profil"]["email"].";")) {
            $tab = mysqli_fetch_row($resultSQL);
			if($tab[0] != "0"){
                mysqli_free_result($resultSQL);
                header('location:../Inscription_Connexion.php?message=Email déjà utilisé&form=Register');
                exit;
            }
            mysqli_free_result($resultSQL);
        } 
        // Ajout donnée dans la base donnée

        $sql = "INSERT INTO Users VALUES (NULL,'".$_SESSION['VAR_profil']['email']."', '".$_SESSION['VAR_profil']['lastName']."', '".$_SESSION['VAR_profil']['firstName']."', '".$_SESSION['VAR_profil']['passwd']."')";
        if(mysqli_query($link, $sql)) {
			mysqli_close($link);
            header('location:../index.php?message=Connecté');
            exit;
        } else {
            echo 'Error Occured';
        }  
    } echo 'not connected';
?>










