<?php
//phpinfo(INFO_MODULES);

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
//echo htmlspecialchars($_POST['Etat_electrovanne']);
if ($_POST['Etat_electrovanne']==1)
{
        $message = "1";
        //echo "ok -1";
        connectionMqtt($message);

}
if ($_POST['Etat_electrovanne']==0)
{

        $message = "0";
        //echo "ok -0";
        connectionMqtt($message);
}

?>


