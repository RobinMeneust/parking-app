<?php 
session_start();
// Vérifier que les champs ne sont pas vides. 
if(!isset($_POST['email']) || empty($_POST['email']) || !isset($_POST['password']) || empty($_POST['password']) ){
	// Redirection vers connexion.php
	header('location:../Inscription_Connexion.php?message=Vous devez remplir les 2 champs pour vous connecter');
	exit;
}

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

	// Si tout est correcte création de variables simplifiées et sécurisées.
	$identifiant = $_POST['email'];
	$passwd = $_POST['passwd'];	

	// verification Identifiant et mdp

	if($resultSQL = mysqli_query($link,"SELECT lastName, firstName, passwd, email FROM Users;")) {
		while($tab = mysqli_fetch_assoc($resultSQL)){
			if($identifiant == $tab["email"] &&  password_verify($passwd, $tab["passwd"])) {
				$VAR_profil = Array();
				// Remplir les informations de la session
				$VAR_profil['lastName'] = $tab["lastName"];
				$VAR_profil['firstName'] = $tab["firstName"];
				$VAR_profil['passwd'] = $tab["passwd"];
				$VAR_profil['email'] = $tab["email"];

				$_SESSION["VAR_profil"] = $VAR_profil;
				mysqli_free_result($resultSQL);
				// Redirection vers la page d'accueil
				header('location:../index.php?message=Connecté'); //Remplacé index par Profile.php
				exit;
			}
		}
		mysqli_free_result($resultSQL);
		header('location:../Inscription_Connexion.php?message=Le mail et/ou le mot de passe ne sont pas bons');
		exit;
	}	
} 

 ?>