
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


if(gettype($Nom) == "string" && (strlen($Nom)>0 && strlen($Nom)<40)){
    if(gettype($Prenom) == "string" && (strlen($Prenom)>0 && strlen($Prenom)<40)){
        if(checkdate($datecontact)){
            if(filter_var($mail, FILTER_VALIDATE_EMAIL) == true && (strlen($mail)>0 && strlen($mail)<40)){
                if($gender == "male" || $gender == "female"){
                    if(checkdate($naissance)){
                        if($typeMessage == "probleme" || $typeMessage == "question" || $typeMessage == "suggestion" || $typeMessage == "message"){
                            if(gettype($sujet) == "string" && (strlen($sujet)>0 && strlen($sujet)<40)){
                                if(gettype($contenu) == "string" && (strlen($contenu)>0 && strlen($contenu)<70)){
                                    fwrite($file, $Nom.";".$Prenom.";".$datecontact.";".$mail.";".$gender.";".$naissance.";".$typeMessage.";".$sujet.";".$contenu."\r\n");
                                    fclose($file);
                                    echo "Merci pour votre confiance monsieur ".$Nom. "! Votre message est transmis à nos équipes qui vous contacterons dans les plus brèves délais." ;
                                }else{
                                    $erreur=$erreur . "Contenu";
                                }
                            }else{
                                if($erreur != "erreur="){
                                    $erreur=$erreur . "_et_";
                                }
                                $erreur=$erreur . "Sujet";
                            }
                        }else{
                            if($erreur != "erreur="){
                                $erreur=$erreur . "_et_";
                            }
                            $erreur=$erreur . "Type_du_Message";
                        }
                    }else{
                        if($erreur != "erreur="){
                            $erreur=$erreur . "_et_";
                        }
                        $erreur=$erreur . "Date_de_Naissance";
                    }
                }else{
                    if($erreur != "erreur="){
                        $erreur=$erreur . "_et_";
                    }
                    $erreur=$erreur . "Genre";
                }
            }else{
                if($erreur != "erreur="){
                    $erreur=$erreur . "_et_";
                }
                $erreur=$erreur . "Email";
            }
        }else{
            if($erreur != "erreur="){
                $erreur=$erreur . "_et_";
            }
            $erreur=$erreur . "Date_du_Message";
        }
    }else{
        if($erreur != "erreur="){
            $erreur=$erreur . "_et_";
        }
        $erreur=$erreur . "Prenom";
    }
}else{
    if($erreur != "erreur="){
        $erreur=$erreur . "_et_";
    }
    $erreur=$erreur . "Nom";
}
if($erreur != "erreur="){
   header('location:Formulaire.html?' . $erreur);
}
?>


