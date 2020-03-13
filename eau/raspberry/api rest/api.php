<?php


header('Content-Type: application/json'); //Indique que l'on envoi un fichier j$




//var_dump($_GET);  //Affiche ce que contient POST
include ("Mqtt.php");
include ("data_base.php");  //Inclue le fichier
echo json_encode($resultat);  //Renvoie le resultat dans un format json
?>
