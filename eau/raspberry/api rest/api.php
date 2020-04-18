<?php
header('Content-Type: application/json'); //Indique que l'on envoi un fichier JSON
include ("Mqtt.php");
include ("data_base.php");  //Inclue le fichier
echo json_encode($resultat);  //Renvoie le resultat dans un format json
?>
