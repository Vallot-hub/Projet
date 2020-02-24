<?php
$ip="127.0.0.1";       //@ IP du serveur, ici localhost
$utilisateur="api";     //Nom d'utilisateur de la base de donnée
$mot_de_passe="snir";     // Mot de passe de la base de donnée
$base_de_donne="Projet";   //Nom de la base de donnée
$i=0;



$base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


if($base_donne->connect_error==true)   // test la connection
{
echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
}


if ($_GET==NULL)   //Regarde le comptenue de la variable POST. Si vide lit toute la base de donnée
{
    $requete = "SELECT * FROM Eau;";  //Requéte général
}
else if ($_GET['Conso']!=NULL)
{
    $requete = "SELECT * FROM `Eau` WHERE Conso='$_GET[Conso]'; ";
}
else if ($_GET['Electrovanne']!=NULL)
{
    $requete = "SELECT * FROM `Eau` WHERE Electrovanne='$_GET[Electrovanne]'; ";
}
$reçu = $base_donne->query($requete);



if($reçu->num_rows>0)  //Si on reçoit quelle que chose
{
    while($resultat[$i]=$reçu->fetch_assoc())
    {
        $i++;
    }
}


$base_donne->close();
?>
