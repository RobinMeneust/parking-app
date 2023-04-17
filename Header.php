<?php

if(session_status() != PHP_SESSION_ACTIVE){
	session_start();
}

?>

<header>
  <div class="logo">
    <a href="./index.php">PARK'O TOP</a>
  </div>

  <div class="search-bar">
    <form id="search-address-form" action="index.php" method="post">
      <input id="search-address-text" name="search-address-text" type="text" placeholder="Rechercher sur Park'O Top">
      <input type="submit" name="submit" value="Rechercher">
    </form>
  </div>

  <div class="account">
    <a href="<?php echo (isset($_SESSION['VAR_profil']) ? "profile.php" : "registerLogin.php"); ?>"><?php echo (isset($_SESSION['VAR_profil'])? "Profil" : "Se connecter"); ?></a>
  </div>
</header>