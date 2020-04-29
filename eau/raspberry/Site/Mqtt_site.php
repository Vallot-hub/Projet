<?php
//echo $_POST['Etat'];
use Mosquitto\Client;

function connectionMqtt($message)
{

define('BROKER', '127.0.0.1');
define('PORT', 1883);
define('CLIENT_ID', "pubclient_" + getmypid());

$client = new Mosquitto\Client(CLIENT_ID);
$client->connect(BROKER, PORT, 60);
$client->publish('gestion', $message, 0, false);
}

if ($_POST['Etat']==1)
{
   connectionMqtt("1");
}
if ($_POST['Etat']==0) 
{
   connectionMqtt("0");
}

sleep ( 2 );
/** Connection à la base de donnée **/
$ip="127.0.0.1";       //@ IP du serveur, ici localhost
$utilisateur="api";     //Nom d'utilisateur de la base de donnée
$mot_de_passe="snir";     // Mot de passe de la base de donnée
$base_de_donne="Projet";   //Nom de la base de donnée



$base_donne = new mysqli($ip,$utilisateur,$mot_de_passe,$base_de_donne);   //connection a la base de donnée


if($base_donne->connect_error==true)   // test la connection
{
echo "Echec de la connexion: ".$base_donne->connect_error;   //message d'erreur
}

  $requete = "SELECT Date, Electrovanne FROM `Electrovanne` ORDER BY Id_electrovanne DESC LIMIT 0,10;";

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
  /** Fin de la connection à la base de donnée **/
  $base_donne->close();

  $resultat=array_reverse($resultat);
    echo json_encode($resultat);  
?>

