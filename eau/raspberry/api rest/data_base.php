<?php
$ip="127.0.0.1";       //@ IP du serveur, ici localhost
$utilisateur="api";     //Nom d'utilisateur de la base de donnée
$mot_de_passe="snir";     // Mot de passe de la base de donnée
$base_de_donne="Projet";   //Nom de la base de donnée




$base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


if($base_donne->connect_error==true)   // test la connection
{
echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
}


if (isset($_GET['Id']))   //Regarde le comptenue de la variable GET. Si vide lit toute la base de donnée
{
    $requete = "SELECT Id,Date,Conso,Debit FROM `Eau` WHERE Id='$_GET[Id]'; ";

}
else if (isset($_GET['Conso']))
{
    $requete = "SELECT Id,Date,Conso,Debit FROM `Eau` WHERE Conso='$_GET[Conso]'; ";
}

else if (isset($_GET['Electrovanne']))
{
    $requete = "SELECT Id,Date,Conso,Debit FROM `Eau` WHERE Electrovanne='$_GET[Electrovanne]'; ";
}

else if (isset($_GET['Debit']))
{
    $requete = "SELECT Id,Date,Conso,Debit FROM `Eau` WHERE Electrovanne='$_GET[Debit]'; ";
}

else if (isset($_POST['Date']))
{
    $requete = "SELECT Id,Date,Conso,Debit FROM `Eau` WHERE Date='$_POST[Date]'; ";
}

else
{
    $requete = "SELECT Id,Date,Conso,Debit FROM Eau;"; //Requéte général
}

$reçu = $base_donne->query($requete);

if($reçu->num_rows>0)  //Si on reçoit quelle que chose
        {
            while($ligne=$reçu->fetch_assoc())
            {
                $resultat[]=$ligne;
            }
        }
        else
        {
            $resultat=NULL;
        }
        

$base_donne->close();
?>



