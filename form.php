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
		<body>
				<div class="containerForm">
			<div class="form">
				<div class="contact-info">
					<h3 class="title">Demande de contact</h3>
					<p class="text">
					Un problème ? Une question ? Une suggestion ? Ou juste envie de nous envoyer un message ? N'hésitez pas à utiliser ce formulaire pour prendre contact avec l'équipe Park'O Top !
					</p>
					<?php
					$error = $_GET['error'] ?? false;
					$success = $_GET['success'] ?? false;
					if (gettype($error) == "string" && (strlen($error) > 0)) //if we get redirected here with an error, we print it
					{
						echo '<p style="color:red; width:300px; overflow-wrap: break-word; font-size:20px;">';
						echo $error;
						echo '</p>';
					}

					if (gettype($success) == "string" && (strlen($success) > 0)) //if the form was a success, we print it
					{
						echo '<p style="color:green; width:300px; overflow-wrap: break-word; font-size:20px;">';
						echo $success;
						echo '</p>';
					}
					?>
				</div>

				<div class="contact-form">  

				<form action="./PHP/formCheck.php" method="post" id="formMsg">
						<h3 class="title">Contactez-nous</h3>
						<div class="input-container">
							<input class="input" type="text" name="firstName" size='40' maxlength='40' required>
							<label for="">Prénom</label>
							<span>Prénom</span>
						</div>
						<div class="input-container">
							<input class="input" type="text" name="lastName" size='40' maxlength='40' required>
							<label for="">Nom</label>
							<span>Nom</span>
						</div>
						<div class="input-container">
						<input required class="input" type="email" name="email" size='40' maxlength='40' required>
							<label for="">Email</label>
							<span>Email</span>
						</div>            
						<div class="input-container">
								<select required name="typeMessage" class="select-type">
										<option selected value="issue">Problème</option>
										<option value="question">Question</option>
										<option value="suggestion">Suggestion</option>
								</select>
							<span style="color: #fff;">Type du message</span>
						</div>

						<div class="input-container">
						<input required class="input" type="text" name="subject" size='40' maxlength='40' required>
							<label for="">Sujet du message</label>
							<span>Sujet du message</span>
						</div>

						<div class="input-container textarea">
							<textarea required name="message" class="input" size='500' maxlength='500'></textarea>
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