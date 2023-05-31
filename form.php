<?php session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Formulaire</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/form.css">
    <?php include_once("head.php"); ?>
</head>

<body>
    <?php include_once("Header.php"); ?>

    </head>

    <?php
    $erreur = $_GET['erreur'] ?? false;
    $success = $_GET['Message'] ?? false;
    echo '<div class=Overall>';
    if (gettype($erreur) == "string" && (strlen($erreur) > 0)) //if we get redirected here with an error, we print it
    {
        echo '<div class="erreur">';
        $pos = strrpos($erreur, ',', 0);
        if (is_int($pos)) {
            $fin = str_split($erreur, $pos - 2);
            $erreur = $fin[0] . str_replace(',', 'et', $fin[1]);
            echo "Veuiller remplir correctement les champs " . $erreur;
        } else {
            echo "Veuiller remplir correctement le champ " . $erreur;
        }
        echo '</div>';
    }

    if (gettype($success) == "string" && (strlen($success) > 0)) //if the form was a success, we print it
    {
        echo '<div class="success">';
        echo $success;
        echo '</div>';
    }


    ?>

    <body>
        <div class="container">
      <div class="form">
        <div class="contact-info">
          <h3 class="title">Demande de contact</h3>
          <p class="text">
          Un problème ? Une question ? Une suggestion? Ou juste envie de nous envoyer un message? N'hésitez pas à utiliser ce formulaire pour prendre contact avec l'équipe Park'o Top !
          </p>
        </div>

        <div class="contact-form">  

        <form action="./PHP/formCheck.php" method="get">
            <h3 class="title">Contactez-nous</h3>
            <div class="input-container">
            <input class="input" type="text" name="datecontact"
            onfocus="this.type='date'" onblur="if(!this.value) this.type='text'" required>
            <label for="">Date du message</label>
              <span>Date du message</span>
            </div>
            <div class="input-container">
              <input class="input" type="text" name="nom" size='40' required>
              <label for="">Nom</label>
              <span>Nom</span>
            </div>
            <div class="input-container">
              <input class="input" type="text" name="prenom" size='40' required>
              <label for="">Prénom</label>
              <span>Prénom</span>
            </div>
            <div class="input-container">
            <input class="input" type="email" name="mail" size='40' required>
              <label for="">Email</label>
              <span>Email</span>
            </div>

            <div class="input-container">
                <input type="radio" name="gender" value="female"><em class="select-gender">Femme</em>
                <input type="radio" name="gender" value="male"><em class="select-gender">Homme</em>
                <span style="color: #fff;">Genre</span>
            </div>
            
            <div class="input-container">
            <input class="input" type="text" name="naissance" required onfocus="this.type='date'" onblur="if(!this.value) this.type='text'">
            <label for="">Date de naissance</label>
              <span>Date de naissance</span>
            </div>
            
            <div class="input-container">
                <select name="typeMessage" class="select-type">
                    <option value="probleme">Problème</option>
                    <option value="question">Question</option>
                    <option value="suggestion">Suggestion</option>
                    <option value="message">Message Lambda</option>
                </select>
              <span style="color: #fff;">Type du message</span>
            </div>

            <div class="input-container">
            <input class="input" type="text" name="sujet" size='40' required>
              <label for="">Sujet du message</label>
              <span>Sujet du message</span>
            </div>

            <div class="input-container textarea">
              <textarea name="contenu" class="input"></textarea>
              <label for="">Message</label>
              <span>Message</span>
            </div>
            <input type="submit" value="Soumettre le formulaire" class="btn" />
          </form>
        </div>
      </div>
    </div>
    <script src="./JS/form.js"></script>
        <?php include_once("Footer.php"); ?>
    </body>
</html>