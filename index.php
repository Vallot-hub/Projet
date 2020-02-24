<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title> Site web d'affichage des mesures </title>
    </head>
    <body>
        <?php
            echo "Bienvenue sur le site web d'affichage des mesures en PHP, CSS, HTML et Javascript.<br>";
            echo " Voici le contexte : <br><br>";
            echo " Une maison secondaire de bord de mer est equipée d'une installation domotique. Celle-ci nécessite la mesure de la consommation d'eau et la détection d'éventuelles fuites d'eau. <br>
            Dans le cas où une fuite est détectée, une electrovanne coupe l'alimentation en eau. La fonction est assurée par un compteur d'eau à impulsion et une electrovanne connectés
            sur une carte ESP8266 nodeMCU V3. Les données sont transmises à un server web, broker, mqtt en wifi.<br>
            Ces données seront affichées par l'intermédiaire du navigateur du téléviseur du salon et depuis n'importe quel poste connecté sur internet. De plus, elles seront accessibles depuis une application Android.";
        ?>
    </body>
</html>
