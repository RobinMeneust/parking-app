
 <?php
$file =fopen("infoContact.json","a+") or die ("Fail");

$Nom =$_GET['Nom'];
$Prenom =$_GET['Prenom'];
$datecontact =$_GET['datecontact'];
$mail =$_GET['mail'];
$gender =$_GET['gender'];
$naissance =$_GET['naissance'];
$typeMessage =$_GET['typeMessage'];
$sujet =$_GET['sujet'];
$contenu =$_GET['contenu'];
$erreur="erreur=";
//VERIFICATIONS VALIDITE ELEMENTS :

if(!(gettype($Nom) == "string" && (strlen($Nom)>0 && strlen($Nom)<40))) {
    $erreur=$erreur . "Nom";
}

if(!(gettype($Prenom) == "string" && (strlen($Prenom)>0 && strlen($Prenom)<40))){
      if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Prenom";
}

if(!((gettype(explode("-",$datecontact,3)[0]) == "int" && gettype(explode("-",$datecontact,3)[1]) == "int" && gettype(explode("-",$datecontact,3)[2]) == "int") && checkdate(explode("-",$datecontact,3)[0],explode("-",$datecontact,3)[1],explode("-",$datecontact,3)[2]))){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Date_du_Message";
}

if(!(filter_var($mail, FILTER_VALIDATE_EMAIL) == true && (strlen($mail)>0 && strlen($mail)<40))){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Email";
}

if(!($gender == "male" || $gender == "female")){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Genre";
}

if(!((gettype(explode("-",$naissance,3)[0]) == "int" && gettype(explode("-",$naissance,3)[1]) == "int" && gettype(explode("-",$naissance,3)[2]) == "int") && checkdate(explode("-",$naissance,3)[0],explode("-",$naissance,3)[1],explode("-",$naissance,3)[2]))){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Date_de_Naissance";
}

if(!($typeMessage == "probleme" || $typeMessage == "question" || $typeMessage == "suggestion" || $typeMessage == "message")){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Type_du_Message";
}

if(!(gettype($sujet) == "string" && (strlen($sujet)>0 && strlen($sujet)<40))){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Sujet";
}

if(!(gettype($contenu) == "string" && (strlen($contenu)>0 && strlen($contenu)<70))){
    if($erreur != "erreur="){
        $erreur=$erreur . "_,_";
    }
    $erreur=$erreur . "Contenu";
}

//RENVOI ERREUR / VALIDATION
if($erreur != "erreur="){
   header('location:Formulaire.html?' . $erreur);
}
else{
    fwrite($file, $Nom.";".$Prenom.";".$datecontact.";".$mail.";".$gender.";".$naissance.";".$typeMessage.";".$sujet.";".$contenu."\r\n");
    fclose($file);
    echo "Merci pour votre confiance monsieur ".$Nom. "! Votre message est transmis à nos équipes qui vous contacterons dans les plus brefs délais." ;
}

//ME    SSAGE RETENU
echo "<br><br>Info remplies dans le formulaire précédent :";
echo "<br>Nom = ";
echo $Nom;
echo "<br>Prenom = ";
echo $Prenom;
echo "<br>Date de Contact = ";
echo $datecontact;
echo "<br>Adresse Mail = ";
echo $mail;
echo "<br>Genre = ";
echo $gender;
echo "<br>Date de Naissance =";
echo $naissance;
echo "<br>Type du Message = ";
echo $typeMessage;
echo "<br>Sujet = ";
echo $sujet;
echo "<br>Contenu du Mail : <br>";
echo $contenu;
?>


