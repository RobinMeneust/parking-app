<?php session_start(); 
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Formulaire</title>
        <link rel="stylesheet" type="text/css" href="./assets/css/coucou.css">
        <?php include_once("head.php"); ?>
    </head>

    <body>
        <?php include_once("Header.php"); ?>

</head>

<?php
$erreur =$_GET['erreur']?? false;
$success = $_GET['Message']?? false;
echo'<div class=Overall>';
if(gettype($erreur) == "string" && (strlen($erreur)>0))//if we get redirected here with an error, we print it
{
    echo'<div class="erreur">';
    $pos = strrpos($erreur,',',0);
    if(is_int($pos))
    {
        $fin= str_split($erreur, $pos-2);
        $erreur=$fin[0].str_replace(',','et',$fin[1]);
        echo "Veuiller remplir correctement les champs ".$erreur;
    }
    else{
        echo "Veuiller remplir correctement le champ ".$erreur;
    }
    echo'</div>';
}

if(gettype($success) == "string" && (strlen($success)>0))//if the form was a success, we print it
{
    echo'<div class="success">';
    echo $success;
    echo'</div>';
}


?>

<body>
<div class="contact">

 <form action="./PHP/formCheck.php" method="get"><!--Send it to formCheck-->

    <div class="contactez-nous">
 <h1 style="text-align:center">Demande de contact</h1>
<p>Un problème? Une question? Une suggestion? Ou juste envie de nous envoyer un message? N’hésitez pas à utiliser ce formulaire pour prendre contact avec l'équipe Park'o Top !</p>

   

<table style="width: 70%;"--><!--form with all the information we need-->

 <tr>
          <td>Date du message</td>
          <td><input type="date" name="datecontact" placeholder="jj/mm/aaaa" required></td>
      </tr>

 <tr>
          <td>Nom </td>
          <td><input type="text" name="Nom" size='40' placeholder="Entrez votre nom" required></td>
      </tr>

 <tr>
          <td>Prénom </td>
          <td><input type="text" name="Prenom" size='40' placeholder="Entrez votre prénom" required></td>
      </tr>

 <tr>
          <td>Email </td>
          <td><input type="email" name="mail" size='40' placeholder="monmail@messagerie.org" required></td>
      </tr>


<tr>
<td>Genre </td>
        <td style="width: 80%;">
  <form>
             <input type="radio" name="gender" value="female">Femme
             <input type="radio" name="gender" value="male">Homme
</form>
    </td>
    </tr>



 <tr>
          <td>Date de naissance</td>
          <td><input type="date" name="naissance" placeholder="jj/mm/aaaa" required></td>
      </tr>

      <tr>
          <td>Type du message </td>

          <td><select name="typeMessage" >
             <option value="probleme">Problème</option>
              <option value="question">Question</option>
               <option value="suggestion">Suggestion</option>
               <option value="message">Message Lambda</option>
          </select></td>

      </tr>

 <tr>
          <td>Sujet: </td>
          <td><input type="text" name="sujet" size='40' placeholder="Entrez le sujet de votre mail" required></td>
      </tr>


 <tr>
          <td>Contenu: </td>
          <td><input type="text" name="contenu" size='70'style="height:110px;" placeholder="Tapez ici votre mail" required/></td>
      </tr>


    </table>
    </fieldset>

  <p style="margin-left: 500px;">
          <input type="submit" value="Soumettre formulaire">
            </input>
    
      </p>
  </form>
</div>
</div>
<?php include_once("Footer.php"); ?>
  </body>
</html>