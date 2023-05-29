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
    <a href=<?php echo isset($_SESSION['VAR_profil']) ? '#' : '"registerLogin.php" style="--clr:#6eff3e" id="connectButton"'; ?>>
      <?php echo isset($_SESSION['VAR_profil']) ? '<img id="user_logo" src="./assets/img/user_picture.jpg" onclick="toggleMenu()" alt="" />' : '<span>Se connecter</span><i></i>'; ?>
    </a>  

    <div class="sub-menu-wrap" id="subMenu">
      <div class="sub-menu">
        <div class="user-info">
          <img id="user_logo" src="./assets/img/user_picture.jpg" alt="" />
          <!--<h2></h2>-->
          <h2><?php echo $_SESSION['VAR_profil']['firstName'] . " " .$_SESSION['VAR_profil']['lastName']?></h2>
        </div>
        <hr/>

        <a href="./profile.php" class="sub-menu-link">
          <img src="./assets/img/edit.png">
          <p>Edit profile</p>
          <span>></span>
        </a>
        <a href="./profile.php" class="sub-menu-link">
          <img src="./assets/img/setting.png">
          <p>Paramètre & Historique</p>
          <span>></span>
        </a>
        <a href="./form.php" class="sub-menu-link">
          <img src="./assets/img/setting.png">
          <p>Help & support</p>
          <span>></span>
        </a>
        <a href="#" class="sub-menu-link">
          <form action="PHP/signOut.php" methode="POST">
            <button style="--clr:#ff1867" id="disconnectButton"><span>Se Déconnecter</span><i></i></button>
          </form>
        </a>
      </div>
    </div>
  </div>

<script>
  let subMenu = document.getElementById("subMenu");

  function toggleMenu() {
    subMenu.classList.toggle("open-menu");
  }

</script>
</header>

