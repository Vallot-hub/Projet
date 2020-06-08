<?php
//phpinfo(INFO_MODULES);   //debug verification

use Mosquitto\Client;

function connectionMqtt($message)
{

define('BROKER', '127.0.0.1');
define('PORT', 1883);
define('CLIENT_ID', "pubclient_PHP";

$client = new Mosquitto\Client(CLIENT_ID);
$client->connect(BROKER, PORT);
$client->publish('gestion', $message, 0);
//////////////////topic/////message//Qos//
}



if (isset($_POST['Etat_electrovanne']))
{

        if ($_POST['Etat_electrovanne']==1)
        {
                $message = "1";
                //echo "ok -1";   //debug
                connectionMqtt($message);

        }
        if ($_POST['Etat_electrovanne']==0)
        {

                $message = "0";
                //echo "ok -0";   //debug
                connectionMqtt($message);
        }
}
?>



