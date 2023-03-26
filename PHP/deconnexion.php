<?php
    
		session_start();
		unset($_SESSION["VAR_profil"]);

    	header('location:../Inscription_Connexion.php?message=Vous vous êtes déconnecté');
  
?>