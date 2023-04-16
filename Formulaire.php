<?php session_start(); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Formulaire</title>
        <?php include_once("head.php"); ?>
    </head>

    <body>
        <?php include_once("Header.php"); ?>
            <p id="p1">Hello World!</p>
<div class="contact">

 <form action="informulaire.php" method="get"><!--pour pouvoir envoyer a informulaire-->

    <div class="contactez-nous">
 <h1 style="text-align:center">Demande de contact</h1>
<p>Un problème? Une question? Une suggestion? Ou juste envie de nous envoyer un message? N’hésitez pas à utiliser ce formulaire pour prendre contact avec l'équipe Park'o Top !</p>

   

<table style="width: 70%;"-->

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
          <td><input type="text" name="Prénom" size='40' placeholder="Entrez votre prénom" required></td>
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
          <button type="button" onclick="quoi"> Soumettre formulaire
          </button>
    
      </p>
 
  </form>
  </body>
  <script>
    if(/[?&]erreur/.test(window.location.search))
    {  
        if(/_/.test(window.location.search))
        { 
            
            a = window.location.search.slice(8).split("_");
            erreur = "";
            for(i of a)
            {
                if(i != a.slice(-1)){
                    if(erreur.slice(-1) == "," || i == ",")
                        erreur=erreur+i;
                    else
                        erreur=erreur+" "+i;
                }
                else{
                    erreur=erreur.substring(0, erreur.length - 1)+" et "+i+".";
                }
            }
        }
        else
        {
            a = window.location.search.slice(8);
        }
        document.getElementById("p1").innerHTML= ("Veuiller remplir correctement les champs " + erreur);
    }
  </script>
</html>