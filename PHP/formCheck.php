<?php session_start();

$file =fopen("../data/infoContact.json","a+") or die ("Fail");

$Nom =$_GET['Nom'];
$Prenom =$_GET['Prenom'];
$datecontact =$_GET['datecontact'];
$email =$_GET['mail'];
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
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Prenom";
}

if(!(((string)(int) explode("-",$datecontact,3)[0] == explode("-",$datecontact,3)[0] && (string)(int) explode("-",$datecontact,3)[1] == explode("-",$datecontact,3)[1] && (string)(int) explode("-",$datecontact,3)[2] == explode("-",$datecontact,3)[2]) && checkdate(explode("-",$datecontact,3)[1],explode("-",$datecontact,3)[2],explode("-",$datecontact,3)[0]))){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Date+du+Message";
}

if(!(filter_var($email, FILTER_VALIDATE_EMAIL) == true && (strlen($email)>0 && strlen($email)<40))){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Email";
}

if(!($gender == "male" || $gender == "female")){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Genre";
}

if(!(((string)(int) explode("-",$naissance,3)[0] == explode("-",$naissance,3)[0] && (string)(int) explode("-",$naissance,3)[1] == explode("-",$naissance,3)[1] && (string)(int) explode("-",$naissance,3)[2] == explode("-",$naissance,3)[2]) && checkdate(explode("-",$naissance,3)[1],explode("-",$naissance,3)[2],explode("-",$naissance,3)[0]))){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Date+de+Naissance";
}

if(!($typeMessage == "probleme" || $typeMessage == "question" || $typeMessage == "suggestion" || $typeMessage == "message")){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Type+du+Message";
}

if(!(gettype($sujet) == "string" && (strlen($sujet)>0 && strlen($sujet)<40))){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Sujet";
}

if(!(gettype($contenu) == "string" && (strlen($contenu)>0 && strlen($contenu)<400))){
    if($erreur != "erreur="){
        $erreur=$erreur . "+,+";
    }
    $erreur=$erreur . "Contenu+" . $contenu;
}

//RENVOI ERREUR / VALIDATION
if($erreur != "erreur="){
   header('location:../form.php?' . $erreur);
}

/*//MESSAGE RETENU
echo "<br><br>Info remplies dans le formulaire précédent :";
echo "<br>Nom = ";
echo $Nom;
echo "<br>Prenom = ";
echo $Prenom;
echo "<br>Date de Contact = ";
echo $datecontact;
echo "<br>Adresse Mail = ";
echo $email;
echo "<br>Genre = ";
echo $gender;
echo "<br>Date de Naissance =";
echo $naissance;
echo "<br>Type du Message = ";
echo $typeMessage;
echo "<br>Sujet = ";
echo $sujet;
echo "<br>Contenu du Mail : <br>";
echo $contenu;*/

// Import PHPMailer classes into the global namespace 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception; 
 
// Include library files 
require 'PHPMailer/Exception.php'; 
require 'PHPMailer/PHPMailer.php'; 
require 'PHPMailer/SMTP.php'; 
 
// Create an instance; Pass `true` to enable exceptions 
$mail = new PHPMailer; 
 
// Server settings 
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;    //Enable verbose debug output 
$mail->isSMTP();                            // Set mailer to use SMTP 
$mail->Host = 'smtp.gmail.com';           // Specify main and backup SMTP servers 
$mail->SMTPAuth = true;                     // Enable SMTP authentication 
$mail->Username = 'baat01.p@gmail.com';       // SMTP username 
$mail->Password = 'irrufbegunznzfjb';         // SMTP password 
$mail->SMTPSecure = 'ssl';                  // Enable TLS encryption, `ssl` also accepted 
$mail->Port = 465;                          // TCP port to connect to 
 
// Sender info 
$mail->setFrom('parkotop@app.com', 'ParkOTop'); 
 
// Add a recipient 
$mail->addAddress('baat01.p@gmail.com'); //adresse de reception
 
//$mail->addCC('cc@example.com'); 
//$mail->addBCC('bcc@example.com'); 
 
// Set email format to HTML 
$mail->isHTML(true); 
 
// Mail subject 
$mail->Subject = 'ParkOTop : ' . $typeMessage; 
 
// Mail body content 
$bodyContent = '<h1>'. $sujet . "</h1>"; 
$bodyContent .= '<p>' . $contenu . '</p>'; 
$bodyContent .=  '<br><p> Message envoyé par '. $Nom ." " . $Prenom ." (". $naissance.") le ".$datecontact.". Pour le contacter : ".$email."</p>";
$mail->Body    = $bodyContent; 
 
// Send email 
if(!$mail->send()) { 
    fwrite($file, $Nom.";".$Prenom.";".$datecontact.";".$email.";".$gender.";".$naissance.";".$typeMessage.";".$sujet.";".$contenu.";".$email->ErrorInfo."\r\n");
    fclose($file);
    header('location:../form.php?Message=' . "<br>Merci pour votre confiance monsieur ".$Nom. "! Votre message est enregistré et nos équipes vont l'examiner pour vous contacter dans les plus brefs délais. ");
} else { 
    header('location:../form.php?Message=' . "<br>Merci pour votre confiance monsieur ".$Nom. "! Votre message est transmis à nos équipes qui vous contacterons dans les plus brefs délais. " );
}


?>


