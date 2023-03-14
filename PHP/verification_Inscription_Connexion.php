<?php

session_start();

$VAR_profil = Array();





if(isset($_POST["nom"]) && !empty($_POST["nom"]) && !preg_match("/[^A-Za-z]/", $_POST["nom"])) {
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
    $_SESSION["password"] = $_POST["password"];
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


unset($_SESSION["identifiant"]);
unset($_SESSION["prenom"]);
unset($_SESSION["nom"]);
unset($_SESSION["password"]);
unset($_SESSION["Confirm_password"]);
unset($_SESSION["genre"]);
unset($_SESSION["age"]);
unset($_SESSION["email"]);
unset($_SESSION["telephone"]);
unset($_SESSION["adresse"]);
unset($_SESSION["code_postal"]);
unset($_SESSION["metier"]);
unset($_SESSION["valid_conditions"]);

/*
//Verifier si ce sont les meme mails
if(isset($_SESSION["email"]) != isset($_POST["confirmEmail"])){
    
    $_SESSION["confirmEmail"] = "error";
    echo 'contenu confirmemail' . $_SESSION["confirmEmail"] .'<br>' ;
echo 'contenu email' . $_SESSION["email"].'<br>' ;
}*/


$_SESSION["connecte"] = 0;



// Vérifier l'email n'est pas déjà utilisés
$usersFile = file_get_contents("../data/users.json");
$json = json_decode($usersFile); // Objet
$users = $json->users;

foreach($users as $user) {
	if($user->email == $_POST["email"]) {
		header('location:../inscription.php?message=Cet email est déjà utilisé.');
		exit;
	}
}


$_SESSION["VAR_profil"]= $VAR_profil;


if ( $_SESSION["VAR_profil"]["prenom"] == "error" || $_SESSION["VAR_profil"]["nom"] == "error" ||   $_SESSION["VAR_profil"]["password"] == "error" || $_SESSION["VAR_profil"]["email"]  == "error" || $_SESSION["VAR_profil"]["adresse"]  == "error"|| $_SESSION["VAR_profil"]["code_postal"] =="error" || $_SESSION["VAR_profil"]["valid_conditions"] =="error" ) {
     
    header('location:../inscription.php?message=QSD');


} else {
    array_push($users, ["identifiant"=> $_POST["identifiant"], "prenom"=> $_POST["prenom"], "nom"=> $_POST["nom"], "password"=> password_hash($_POST["password"], PASSWORD_BCRYPT), "genre"=> $_POST["genre"], "age"=> $_POST["age"], "email"=> $_POST["email"], "telephone"=> $_POST["telephone"],"adresse"=> $_POST["adresse"], "code_postal"=> $_POST["code_postal"], "metier"=> $_POST["metier"], "valid_conditions"=> $_POST["valid_conditions"]]);
    $res = [];
    $res["users"] = $users;

    $json = json_encode($res);
    file_put_contents("../data/users.json", $json);

    // Enregistrer les infos du user
    $_SESSION["connecte"] = 1;
    $VAR_profil["connecte"] = $_SESSION["connecte"];
    unset($_SESSION["connecte"]);
    $_SESSION["VAR_profil"]= $VAR_profil;
    header('location:../index.php?message=Connecté');
    exit;




    

    
}