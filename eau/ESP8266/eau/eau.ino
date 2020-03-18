#include <ESP8266WiFi.h>
#include <PubSubClient.h>

// wifi
const char* ssid     = "Livebox-3002";
const char* password = "aSzy24ZzWm5xrKrumG";

//const char* host = "192.168.5.187";
//const uint16_t port = 80;

// mqtt
const char* mqttServer = "192.168.1.17";
const int mqttPort = 1883;

const char* mqttUser = ""; 
const char* mqttPassword = "";





WiFiClient espClient;  //objet pour le wifi
PubSubClient client(espClient);  //objet pour le Mqtt

void connection_Mqtt();

int compteur_eau = 4;  // pin D2
int electrovanne = 5;  // pin D1
int compt=0;       //contient le nombre de litre/impulsion 
int Etat_electrovanne=0;                  // 0=fermée  1=ouvert
int dernier_litre=0;

void ICACHE_RAM_ATTR nb_impulsion(void);   // ICACHE_RAM_ATTR permet de charger attachInterrupt dans la RAM // permet de compter le nombre d'impulsion donc de litre

void ouverture_electrovanne();
void fermeture_electrovanne();

void envoi_message();

float calcul_debit();
float debit=0;
float dernier_debit=0;

int nb_debit=0;
unsigned long duree;
unsigned long derniere_duree;







void callback(char* topic, byte* payload, unsigned int length)   //rappel
{
  char message_buff[100];
  /** Debug **/ 
  Serial.print("Message reçu sur le topic : ");  
  Serial.print(topic);  //affiche le nom du topic
  Serial.print("   la longueur est :");
  Serial.println(length);    //affiche la longueur du message
  Serial.print("donnée reçu du broker :");
  int i;   //utile pour le for 
  for (i = 0; i < length; i++) // parcours le tableau
  {
  message_buff[i] = payload[i];     //
  }
  message_buff[i] = '\0';  //fin de la chaine

  Serial.println();       //mise en page
  Serial.println("-----------------------");
  Serial.println();

  String msgString = String(message_buff);   //convertie le message en string 
  Serial.println(msgString);   //affiche le message

    
    //envoi_message(); 
    if (msgString == "1")
    {
      ouverture_electrovanne();  
      envoi_message();        //envoi un message de confirmation 
    }
    if (msgString == "0")
    {
      fermeture_electrovanne();
      envoi_message();        //envoi un message de confirmation
    }
  }





void setup() 
{
  
  Serial.begin(9600); // communique avec 9600 baud (vitesse de communication)
  pinMode(compteur_eau, INPUT);   //compteur d'eau brancher sur la pin D2 
  pinMode(electrovanne, OUTPUT);    //electrovanne brancher sur la pin D1
  attachInterrupt(digitalPinToInterrupt(compteur_eau),nb_impulsion,RISING);   //interruption lors qu'il y a front montant
  
  
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
 
  while (WiFi.status() != WL_CONNECTED) 
  {
    delay(500);
    Serial.println("Connection au WiFi..");
  }
  Serial.println("Connectée au réseau WiFi");

  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  
  
  client.setServer(mqttServer, mqttPort);   //definition du server Mqtt
  client.setCallback(callback);   //defini la fonction de retour

/** connection au broker Mqtt**/

  connection_Mqtt();
  
}

 



void loop() 
{
    while (WiFi.status() != WL_CONNECTED) 
  {
    delay(500);
    Serial.println("Connection au WiFi..");
  }
   connection_Mqtt();
    //Serial.println(compt);
    for(int i = 0; i<20 ;i++)
   {
    /* reconnection au broker si elle est perdu */
    client.loop();
    delay(500);
   }
   
   duree=millis();
   debit=calcul_debit(compt-dernier_litre,(duree-derniere_duree)*0.001);
   Serial.print("debit :");
   Serial.println(debit);
   dernier_litre=compt;
   
   
   if (debit==dernier_debit)
   {
      nb_debit++;
   }
   else
   {
    nb_debit=0;
   }

   
   Serial.print("nb_debit :");
   Serial.println(nb_debit);
   Serial.print("temps :");
   Serial.println(duree);
   dernier_debit=debit;
   derniere_duree=duree;
    //Serial.print("wifi : ");
    //Serial.print(WiFi.status());

    /**  teste de la connexion au server mqtt   **/
    connection_Mqtt();
    envoi_message();
}



float calcul_debit(int litre, float temps)
{
    return litre/temps;
}




void connection_Mqtt()
{
    while (!client.connected()) // tant que le client n'est pas connecté
    {
      if (client.connect("ESP8266Client"))   //  connection au broker Mqtt  
      {
        Serial.println("connectée au serveur mqtt");  // affiche par l'USB que l'on est connecté
        client.subscribe("gestion");
      } 
      else                        // si non 
      {
        Serial.print("Erreur Mqtt au niveau : ");  // affiche l'erreur 
        Serial.println(client.state());   // info debloquage + ln=retour à la ligne
        delay(2000);  //  attend 2000ms=2s
      } 
    }
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
  strcat(envoi,":");  //ajoute une donneee à la fin du char envoi
  strcat(envoi,temp);
  str=String(debit);
  str.toCharArray(temp,5);
  strcat(envoi,":");  //ajoute une donneee à la fin du char envoi
  strcat(envoi,temp);
  Serial.println(envoi);


//format de l'envoie en Mqtt                "5:0:1.54"
//............................nb_litre : etat_electrovanne : debit sur 10s.........................//
  
  client.publish("compteur_connecte/conso", envoi);
}





void ouverture_electrovanne()
{
  digitalWrite(electrovanne, HIGH);  //signal continu = 3.3V  
  Etat_electrovanne = 1;    // 1 :L'electrovanne est ouverte (l'eau ne peux)
}






void fermeture_electrovanne()
{
  digitalWrite(electrovanne, LOW);   //signal continu = 0V
  Etat_electrovanne = 0;    // 0 :L'electrovanne est fermé
}





void ICACHE_RAM_ATTR nb_impulsion(void)   // ICACHE_RAM_ATTR permet de charger attachInterrupt dans la RAM // permet de compter le nombre d'impulsions
{
  static unsigned long dernier_temps=0; 
  unsigned long temps = millis();
  if (temps - dernier_temps>100)
  {   //attend 100ms entre les mesures // évite que l'on compte plusieurs fois la même impulsion
    compt++;   // +1 litre !!!
    dernier_temps = temps;  //réinitialise le temps avant la prochaine mesure
  }
}
