<?php


header('Content-Type: application/json'); //Indique que l'on envoi un fichier json




//var_dump($_GET);  //Affiche ce que contient POST
include ("data_base.php");  //Inclue le fichier
echo json_encode($resultat);  //Renvoie le resultat dans un format json
?>