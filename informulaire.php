
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




fwrite($file, $Nom.";".$Prenom.";".$datecontact.";".$mail.";".$gender.";".$naissance.";".$typeMessage.";".$sujet.";".$contenu."\r\n");
fclose($file);


echo "Merci pour votre confiance monsieur ".$Nom. "! Votre message est transmis à nos équipes qui vous contacterons dans les plus brèves délais." ;

?>


