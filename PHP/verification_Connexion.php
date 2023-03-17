<?php 
// Vérifier que les champs ne sont pas vides. 
if(!isset($_POST['email']) || empty($_POST['email']) || !isset($_POST['password']) || empty($_POST['password']) ){
	// Redirection vers connexion.php
	header('location:../Inscription_Connexion.php?message=Vous devez remplir les 2 champs pour vous connecter');
	exit;
}

// Si tout est correcte création de variables simplifiées et sécurisées.
$identifiant = $_POST['email'];
$password = $_POST['password'];	




$usersFile = file_get_contents("../data/users.json");
$json = json_decode($usersFile); // Objet
$users = $json->users;

foreach($users as $user) {
	if($user->email == $_POST["email"] && password_verify($_POST["password"], $user->password)) {
		session_start();
		$VAR_profil = Array();
		// Remplir les informations de la session
		$VAR_profil['prenom'] = $user->prenom;
		$VAR_profil['nom'] = $user->nom;
		$VAR_profil['password'] = $user->password;
		$VAR_profil['email'] = $user->email;
		$VAR_profil['adresse'] = $user->adresse;
		$VAR_profil['code_postal'] = $user->code_postal;
		$VAR_profil['valid_conditions'] = $user->valid_conditions;
		$VAR_profil['connecte'] = 1;

		$_SESSION["VAR_profil"] = $VAR_profil;
	
		// Redirection vers la page d'accueil
		header('location:../index.php?message=Connecté'); //Remplacé index par profil.php
		exit;
	}
}

// Redirection vers connexion.php
header('location:../Inscription_Connexion.php?message=Identifiants invalides');






 ?>