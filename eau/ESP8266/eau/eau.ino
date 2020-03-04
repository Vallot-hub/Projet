#include <ESP8266WiFi.h>
#include <PubSubClient.h>


// wifi
const char* ssid     = "snir";
const char* password = "12345678";

//const char* host = "192.168.5.187";
//const uint16_t port = 80;

// mqtt
const char* mqttServer = "192.168.5.187";
const int mqttPort = 1883;

//const char* mqttUser = ""; 
//const char* mqttPassword = "";





WiFiClient espClient;  //objet pour le wifi
PubSubClient client(espClient);  //objet pour le Mqtt


int compteur_eau = 4;  // pin D2
int electrovanne = 5;  // pin D1
int compt=0;       //contient le nombre de litre/impulsion 
int Etat_electrovanne=0;                  // 0=fermée  1=ouvert
int dernierlitre=0;
void ICACHE_RAM_ATTR nb_impulsion(void);   // ICACHE_RAM_ATTR permet de charger attachInterrupt dans la RAM // permet de compter le nombre d'impulsion donc de litre

void ouverture_electrovanne();
void fermeture_electrovanne();

void envoi_message();
float debit();
float debi=0;

void callback(char* topic, byte* payload, unsigned int length) {
  char message_buff[100];
  /** Debug **/ 
  Serial.print("Message received in topic: ");
  Serial.print(topic);
  Serial.print("   length is:");
  Serial.println(length);
  Serial.print("Data Received From Broker:");
  int i;
  for (i = 0; i < length; i++) 
  {
  message_buff[i] = payload[i];
  }
  message_buff[i] = '\0';  //fin de la chaine

  Serial.println();
  Serial.println("-----------------------");
  Serial.println();

  String msgString = String(message_buff);   //met le message dans un string 
  Serial.println(msgString);

    if (msgString == "1")
    {
      ouverture_electrovanne();
      envoi_message();
    }
    if (msgString == "0")
    {
      fermeture_electrovanne();
      envoi_message();
    }
  }





void setup() 
{
  Serial.begin(9600);
  pinMode(compteur_eau, INPUT);
  pinMode(electrovanne, OUTPUT);
  attachInterrupt(digitalPinToInterrupt(compteur_eau),nb_impulsion,RISING);   //interruption 
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
 
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.println("Connecting to WiFi..");
  }
  Serial.println("Connected to the WiFi network");
  
  
  
  client.setServer(mqttServer, mqttPort);   //definition du server Mqtt
  client.setCallback(callback);   //defini la fonction de retour

/** connection au broker Mqtt**/
while (!client.connected()) 
{
    Serial.println("Connection au broker Mqtt...");
 
    if (client.connect("ESP8266Client")) 
    {
      Serial.println("Mqtt -OK");  
    } 
    else 
    {
      Serial.print("erreur au niveau ");
      Serial.print(client.state());
      delay(2000);
    }
}


  client.subscribe("gestion");
  
}

 



void loop() 
{
  
    Serial.println(compt);
    
    for(int i = 0; i<20 ;i++)
   {
    client.loop();
    delay(500);
     
     /* reconnection au broker si elle est perdu */
     while (!client.connected()) 
    {
      if (client.connect("ESP8266Client"))      
      {
        Serial.println("connectée au serveur mqtt");  
      } 
      else 
      {
        Serial.print("failed with state ");
        Serial.print(client.state());
        delay(2000);
      } 
    }
   }
   debi=debit(compt-dernierlitre,10);
   dernierlitre=compt;
    //Serial.print("wifi : ");
    //Serial.print(WiFi.status());

    /**  teste de la connexion au server mqtt   **/
    while (!client.connected()) 
    {
      if (client.connect("ESP8266Client"))      //reconnection au broker
      {
        Serial.println("connectée au serveur mqtt");  
      } 
      else 
      {
        Serial.print("failed with state ");
        Serial.print(client.state());
        delay(2000);
      } 
    }
  envoi_message();
}



float debit(int litre, float temps)
{
    return litre/temps;
}


void envoi_message()
{
    /*conversion de int en char*/
  char envoi[10];
  char temp[2];
  String str=String(compt);
  str.toCharArray(envoi,5); 
  str=String(Etat_electrovanne);
  str.toCharArray(temp,2);
  strcat(envoi,":");  //ajoute une donneee à la fin du char b
  strcat(envoi,temp);
  str=String(debi);
  str.toCharArray(temp,5);
  strcat(envoi,":");  //ajoute une donneee à la fin du char b
  strcat(envoi,temp);
  Serial.println(envoi);


//format de l'envoie en Mqtt         "5:0"
//............................nb_litre : etat_electrovanne.........................//
  client.publish("compteur_connecte/conso", envoi);
}





void ouverture_electrovanne()
{
  digitalWrite(electrovanne, LOW);    
  Etat_electrovanne = 1;    // 1 :L'electrovanne est ouverte
}







void fermeture_electrovanne()
{
  digitalWrite(electrovanne, HIGH); 
  Etat_electrovanne = 0;    // 0 :L'electrovanne est fermé
}





void ICACHE_RAM_ATTR nb_impulsion(void)
{
  static unsigned long dernier_temps=0; 
  unsigned long temps = millis();
  if (temps - dernier_temps>100)
  {   //attend 100ms entre les mesures // évite que l'on compte plusieurs fois la même impulsion
    compt++;   // +1 litre !!!
    dernier_temps = temps;  //réinitialise le temps avant la prochaine mesure
  }
}
