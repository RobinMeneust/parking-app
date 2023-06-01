<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title> Données Personnelles </title>
        <?php include_once("head.php"); ?>
    </head>

    <body>
        <?php include_once("Header.php"); ?>

        <div class="content">
            <div class="helpcontent">
                <h1> POLITIQUE DE PROTECTION DES DONNEES PERSONNELLES </h1>
                <h2 class="titlehelp" style="text-align: center"> Vous voulez exercer vos droits sur vos données personnelles&nbsp;? Contactez nous en&nbsp;cliquant&nbsp;sur&nbsp;le&nbsp;bouton&nbsp;ci-dessous. </h2>
                <div class="idz_btn_fix">
                    <div id="idzonline">
                        <a href="./form.php" class="chat-cta">
                            <span class="chat-cta-label"> Cliquez ici pour nous contacter </span>
                        </a>
                    </div>
                    <p>
                        <strong> Dernière mise à jour : Juin 2023 </strong>
                    </p>
                    <p>
                        Le groupe PARK'O TOP est soucieux de la protection des données personnelles. Il met en œuvre une démarche d'amélioration continue de sa conformité au Règlement général de protection des données (RGPD), à la Directive ePrivacy, ainsi qu'à la loi n° 78-17 du 6 janvier 1978 dite Informatique et Libertés pour assurer le meilleur niveau de protection à vos données personnelles.
                        <br>
                        Pour toute information sur la protection des données personnelles, vous pouvez également consulter le site de la Commission Nationale de l'Informatique et des Libertés <a href="http://www.cnil.fr" target="_blank" rel="noopener"> www.cnil.fr </a>.
                    </p>
                </div>
            </div>
        </div>
        
        <?php include_once("Footer.php"); ?>
    </body>
</html>