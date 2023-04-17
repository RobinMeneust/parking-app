<?php
    
		session_start();
		unset($_SESSION["VAR_profil"]);

    	header('location:../registerLogin.php?message=Vous vous êtes déconnecté');
  
?>