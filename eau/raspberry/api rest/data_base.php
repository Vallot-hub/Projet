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


if (isset($_POST['Id']))   //Regarde le comptenue de la variable GET. Si vide lit toute la base de donnée
{
    $requete = "SELECT Id,Consommation,Debit,Date FROM `Eau` WHERE Id='$_POST[Id]'; ";
}

else if (isset($_POST['Consommation']))
{
    $requete = "SELECT Id,Consommation,Debit,Date FROM `Eau` WHERE Consommation='$_POST[Consommation]'; ";
}

else if (isset($_POST['Electrovanne']))
{
    $requete = "SELECT Id,Consommation,Debit,Date FROM `Eau` WHERE Electrovanne='$_POST[Electrovanne]'; ";
}

else if (isset($_POST['Debit']))
{
    $requete = "SELECT Id,Consommation,Debit,Date FROM `Eau` WHERE Electrovanne='$_POST[Debit]'; ";
}

else if (isset($_POST['Date']))
{
    $requete = "SELECT Id,Consommation,Debit,Date FROM `Eau` WHERE Date='$_POST[Date]'; ";
}

else if (isset($_POST['DerniereValeur']))
{
    $requete = "SELECT Electrovanne FROM `Electrovanne` ORDER BY Id_electrovanne DESC LIMIT 0,1; ";
}

else
{
    $requete = "SELECT Id,Consommation,Debit,Date FROM Eau;";   //renvoie toute les valeurs de l'api
    //$requete = "SELECT Id,Date,Consommation,Debit FROM Eau ORDER BY id DESC LIMIT 0,1;";   //renvoie la derniere entrée dans la base de donnée
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



