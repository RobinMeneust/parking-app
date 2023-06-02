<?php session_start(); 

$file =fopen("../data/infoContact.json","a+") or die ("Fail");// Create the file if it doesn't exist
fclose($file);

clearstatcache();

if(filesize("../data/infoContact.json") == 0) {
    $arr['messages'] = array();
    file_put_contents('../data/infoContact.json', json_encode($arr));
}

$lastName = "";
$firstName = "";
$dateContact = date("Y-m-d");
$email = "";
$typeMessage = "";
$subject = "";
$message = "";

//We get all the data we need from the url :

if(isset($_POST["lastName"]) && !empty($_POST["lastName"]) && preg_match("/^[A-Za-z-]*$/", $_POST["lastName"]) && (strlen($_POST["lastName"])>0 && strlen($_POST["lastName"])<41)) {
    $lastName = $_POST["lastName"];
} else {
    header('location:../form.php?error=Nom invalide');
    exit;
}

if(isset($_POST["firstName"]) && !empty($_POST["firstName"]) && preg_match("/^[a-zA-Z\s-]*$/", $_POST["firstName"]) && (strlen($_POST["firstName"])>0 && strlen($_POST["firstName"])<41)) {
    $firstName = $_POST["firstName"];
} else {
    header('location:../form.php?error=Prénom invalide');
    exit;
}

if(isset($_POST["email"]) && !empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST["email"];
} else {
    header('location:../form.php?error=Email invalide');
    exit;
}

if(isset($_POST["typeMessage"]) && ($_POST["typeMessage"] == "issue" || $_POST["typeMessage"] == "question" || $_POST["typeMessage"] == "suggestion")) {
    $typeMessage = $_POST["typeMessage"];
} else {
    header('location:../form.php?error=Type de message invalide'.$typeMessage);
    exit;
}

if(isset($_POST["subject"]) && !empty($_POST["subject"]) && gettype($_POST["subject"]) == "string" && (strlen($_POST["subject"])>0 && strlen($_POST["subject"])<41)) {
    $subject = $_POST["subject"];
} else {
    header('location:../form.php?error=Sujet vide ou trop long');
    exit;
}

if(isset($_POST["message"]) && !empty($_POST["message"]) && gettype($_POST["message"]) == "string" && (strlen($_POST["message"])>0 && strlen($_POST["message"])<501)) {
    $message = $_POST["message"];
} else {
    header('location:../form.php?error=Message vide ou trop long');
    exit;
}

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
$mail->isSMTP();                            // Set mailer to use SMTP 
$mail->Host = 'smtp.gmail.com';           // Specify main and backup SMTP servers 
$mail->SMTPAuth = true;                     // Enable SMTP authentication 
$mail->Username = 'parkotop.website@gmail.com';       // SMTP username 
$mail->Password = 'parkotopCeciEstUnTest@';         // SMTP password 
$mail->SMTPSecure = 'ssl';                  // Enable TLS encryption
$mail->Port = 465;                          // TCP port to connect to 
 
// Sender info 
$mail->setFrom('parkotop@app.com', 'ParkOTop'); 
 
// Add a recipient 
$mail->addAddress('parkotop.website@gmail.com'); //receiving address
 
// Set email format to HTML 
$mail->isHTML(true); 
 
// Mail subject 
$mail->Subject = 'ParkOTop : ' . $typeMessage; 
 
// Mail body content 
$bodyContent = '<h1>'. $subject . "</h1>"; 
$bodyContent .= '<p>' . $message . '</p>'; 
$bodyContent .=  '<br><p> Message envoyé par '. $firstName ." " . $lastName ." le ".$dateContact.". Pour le contacter : ".$email."</p>";
$mail->Body    = $bodyContent; 

header("location:../form.php?success=".json_encode($mail));
exit;
// Send email 
if(!$mail->send()) { //we write the informations on a json file if we can't send a mail
    $msg = (object) [
        'lastName' => $lastName,
        'firstName' => $firstName,
        'dateContact' => $dateContact,
        'email' => $email,
        'typeMessage' => $typeMessage,
        'subject' => $subject,
        'message' => $message,
        'error' => $mail->ErrorInfo
    ];

    $previousContent = file_get_contents('../data/infoContact.json');
    $json = json_decode($previousContent, true);
    array_push($json['messages'], $msg);
    $jsonData = json_encode($json, JSON_PRETTY_PRINT);
    file_put_contents('../data/infoContact.json', $jsonData);
    
}
header("location:../form.php?success=" . "<br>Merci pour votre confiance ".$firstName." ".$lastName. "! Votre message est transmis à nos équipes qui vous contacterons dans les plus brefs délais.");

?>