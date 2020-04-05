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
   echo "L'eau coule !!!";
}
if ($_POST['Etat']==0) 
{
   connectionMqtt("0");
   echo "L'eau ne coule pas !!!";
}
?>
