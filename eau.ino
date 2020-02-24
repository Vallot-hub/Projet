#include <ESP8266WiFi.h>
#include <PubSubClient.h>


// wifi
const char* ssid     = "snir";
const char* password = "12345678";


// mqtt
const char* mqttServer = "192.168.5.187";
const int mqttPort = 1883;
//const char* mqttUser = ""; 
//const char* mqttPassword = "";





WiFiClient espClient;
PubSubClient client(espClient);


int compteur_eau = 4;  // pin D2
int electrovanne = 5;  // pin D1
int compt=0;
int Etat_electrovanne=0;                  // 0=fermée  1=ouvert
void ICACHE_RAM_ATTR nb_impulsion(void);   // ICACHE_RAM_ATTR permet de charger attachInterrupt dans la RAM
void ouverture_electrovanne();
void fermeture_electrovanne();



void setup() 
{
  Serial.begin(9600);
  pinMode(compteur_eau, INPUT);
  pinMode(electrovanne, OUTPUT);
  attachInterrupt(digitalPinToInterrupt(compteur_eau),nb_impulsion,RISING);   //interruption 
  WiFi.begin(ssid, password);
 
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.println("Connecting to WiFi..");
  }
  Serial.println("Connected to the WiFi network");
 
  client.setServer(mqttServer, mqttPort);


while (!client.connected()) 
{
    Serial.println("Connecting to MQTT...");
 
    if (client.connect("ESP8266Client")) 
    {
      Serial.println("connected au serveur mqtt");  
    } 
    else 
    {
      Serial.print("failed with state ");
      Serial.print(client.state());
      delay(2000);
    }
}

  
//client.publish("compteur_connecte/conso","0");

 
}

 



void loop() 
{
  Serial.println(compt);
  client.loop();
  char envoi[10];
  char temp[2];

  

  
  String str=String(compt);
  str.toCharArray(envoi,5); 
  String str2=String(Etat_electrovanne);
  str2.toCharArray(temp,2);
  strcat(envoi,":");  //ajoute une donneee à la fin du char b
  strcat(envoi,temp);
  //Serial.println(str2);










  
  client.publish("compteur_connecte/conso", envoi);
  //ouverture_electrovanne();
  fermeture_electrovanne();
  delay(5000);
  //toutes les minutes
  //ouverture_electrovanne();
  //fermeture_electrovanne();
  //delay(1000);
}







void ouverture_electrovanne()
{
  Etat_electrovanne = 1;
  digitalWrite(electrovanne, LOW); 
}







void fermeture_electrovanne()
{
  Etat_electrovanne = 0;
  digitalWrite(electrovanne, HIGH); 
}





void ICACHE_RAM_ATTR nb_impulsion(void)
{
  static unsigned long dernier_temps=0; 
  unsigned long temps = millis();
 if (temps - dernier_temps>100){   //attend 100ms // évite que l'on compte plusieurs fois la même impulsion
  compt++;   // +1 litre !!!
  dernier_temps = temps;  //reinistialise le temps avant la prochaine mesure
}
}
