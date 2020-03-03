<?php
//phpinfo(INFO_MODULES);
use Mosquitto\Client;


define('BROKER', 'localhost');
define('PORT', 1883);
define('CLIENT_ID', "pubclient_" + getmypid());

$client = new Mosquitto\Client(CLIENT_ID);
$client->connect(BROKER, PORT, 60);

while ($client->loop() == 0) {
	$message = "Test message at " . date("Y-m-d H:i:s");
	$client->publish('test', $message, 0, false);
	$client->loop();
	sleep(1);
}
?>
