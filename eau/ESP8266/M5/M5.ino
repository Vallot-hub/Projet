#include <M5StickC.h>
#include <WiFi.h>
#include <PubSubClient.h>

//#include <SPI.h>


// wifi
const char* ssid     = "Livebox-3002";   //nom du point d'acces wifi
const char* password = "aSzy24ZzWm5xrKrumG";   // mot de passe du wifi

// mqtt
const char* mqtt_host = "86.252.153.65";    // Addresse du Broker Mqtt
const int mqtt_port = 1883;    // numero de port de la liaison Mqtt

//const char* mqttUser = ""; 
//const char* mqttPassword = "";





WiFiClient wifi_client;  //objet pour le wifi
PubSubClient Mqtt_client(mqtt_host, mqtt_port, nullptr, wifi_client);  //objet pour le Mqtt

void connexion_Mqtt();   // ce connecte au mqtt
int n_menu=0;    // contiens le numero du menu
int compteur_eau = 26;  // pin D2
int electrovanne = 5;  // pin D1
int compt=0;       //contient le nombre de litre/impulsion 
int Etat_electrovanne=0;                  // 0=fermée  1=ouvert
int dernier_litre=0;    // sert a calculer un Delta pour le debit

void ICACHE_RAM_ATTR nb_impulsion(void);   // ICACHE_RAM_ATTR permet de charger attachInterrupt dans la RAM // permet de compter le nombre d'impulsion donc de litre

void ouverture_electrovanne();   // fonction d'ouverture de l'electrovanne
void fermeture_electrovanne();   //  fonction de fermuture de l'electrovanne

void menu();    // fonction d'affichage du menu correspondant 
void menu_info();     //fonction d'affichage du menu 1 ( information sur la consommation ) 
void menu_conn();     //fonction d'affichage du menu 2 ( information sur la connexion )


void envoi_message();     // fonction d'envoi du message Mqtt

float calcul_debit(int litre, float temps);     //fonction qui calcule le debit
float debit=0;     //contien le debit
float dernier_debit=0;     //contien le dernier debit sert a conparer les valeurs du debit pour la detection de fuite

int nb_debit=0;    //comptien le nombre de mesure de debit identique ( au presque au litre près )
unsigned long duree;    // temps de puis le demarage, utilise pour le calcule du debit
unsigned long derniere_duree;     // comtient le temps de puis le demarage de l'ancienne boucle





void callback(char* topic, byte* payload, unsigned int length)    //fonction appeler lors de la reception d'une requete Mqtt
{
  char message_buff[100];     // contient le message 
  /** Debug **/ 
  Serial.print("Message reçu sur le topic : ");  
  Serial.print(topic);  //affiche le nom du topic
  Serial.print("   la longueur est :");
  Serial.println(length);    //affiche la longueur du message
  Serial.print("donnée reçu du broker :");
  int i;   //utile pour le for 
  for (i = 0; i < length; i++)    // parcours les caractères du message
  {
  message_buff[i] = payload[i];     //
  }
  message_buff[i] = '\0';  //fin de la chaine


  String msgString = String(message_buff);   //convertie le message en string 
  Serial.println(msgString);   //affiche le message

    
    M5.Lcd.setCursor(40, 30);   // met le curseur au centre de l'écran 
    M5.Lcd.setTextSize(1);    // taille du texte
    if (msgString == "1")    // si on a reçu un 1
    {
      ouverture_electrovanne();   // ouverture de l'electrovanne
      char envoi[10]="";
      String msg = "";
      msg = String(Etat_electrovanne);
      msg.toCharArray(envoi,5);
      Mqtt_client.publish("envoi/electrovanne", envoi);  //envoi un message de confirmation
      M5.Lcd.setTextColor(WHITE, BLUE);     // change la couleur de l'arriere plan du text
      M5.Lcd.fillScreen(BLUE);     // écran de couleur bleu
      M5.Lcd.println("circuit ferme");   // écriture sur l'écran
    }
    if (msgString == "0")
    {
      fermeture_electrovanne();   // fermeture de l'électrovanne
      char envoi[10]="";
      String msg = "";
      msg = String(Etat_electrovanne);
      msg.toCharArray(envoi,5);
      Mqtt_client.publish("envoi/electrovanne", envoi);  //envoi un message de confirmation
      M5.Lcd.setTextColor(WHITE, RED);     // change la couleur de l'arriere plan du text en rouge
      M5.Lcd.fillScreen(RED);    // écran de couleur rouge
      M5.Lcd.println("circuit ouvert");
    }
    delay(1000);    // attend 1 seconde pour l'affichage
    M5.Lcd.fillScreen(TFT_BLACK); //efface l'écrant
    M5.Lcd.setTextColor(WHITE, BLACK);  // change la couleur de l'arriere plan en noir
    menu();   // affichage du menu
}






void setup() 
{
  M5.begin();  //démarrage de l'écran 
  M5.Lcd.fillScreen(TFT_BLACK);  // ecran noir
  M5.Lcd.setTextColor(WHITE,BLACK);    // police de couleur blanche et arriere de tu texte en noir
  M5.Lcd.setRotation(3);  //format paysage
  Serial.begin(115200);    // communique avec 115200 baud (vitesse de communication)
  pinMode(compteur_eau, INPUT);   //compteur d'eau brancher sur la pin D2 
  pinMode(electrovanne, OUTPUT);    //electrovanne brancher sur la pin D1
  attachInterrupt(digitalPinToInterrupt(compteur_eau),nb_impulsion,RISING);   //interruption lors qu'il y a front montant
  
  
  
  WiFi.mode(WIFI_STA);   //
  WiFi.begin(ssid, password);  // demarage de la connection WiFi
  while (WiFi.status() != WL_CONNECTED)   // tant que l'on est pas connecté au WiFi
  {
    M5.Lcd.setCursor(0, 0, 2);     //Curceur en haut à gauche et de taille 2
    Serial.print("Connection au WiFi : ");  //affichage via USB
    Serial.println(WiFi.status());
    M5.Lcd.print("Connection au WiFi : ");  //affichage sur l'ecran
    M5.Lcd.println(WiFi.status());
    delay(500);
  }
  
  M5.Lcd.fillScreen(TFT_BLACK);  // ecran noir ( éfface le texte ecrit précédemment
  Serial.println("Connectée au réseau WiFi");
  
  Mqtt_client.setCallback(callback);   //defini la fonction de retour

/** connection au broker Mqtt**/
  
  connexion_Mqtt();  // connexion en Mqtt
  menu();
}
 



void loop() 
{
    //Serial.println(compt);
    
    for(int i = 0; i<200 ;i++)
   {
    M5.update();
    Mqtt_client.loop();
    delay(50);
    if(M5.BtnB.wasPressed())
    {
      M5.Lcd.fillScreen(TFT_BLACK); //efface l'écrant
      if (n_menu>0)
      {
        n_menu=0;  
      }
      else
      {
        n_menu++;
      }
    }
    menu();
    if(M5.BtnA.wasPressed())
    {
      
    }
     /* reconnection au broker si elle est perdu */
     connexion_Mqtt();
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
    connexion_Mqtt();
    envoi_message();
}



float calcul_debit(int litre, float temps)
{
    return litre/temps;
}




void connexion_Mqtt()
{
       while (!Mqtt_client.connected()) // tant que le client n'est pas connecté
    {
      if (Mqtt_client.connect("ESP8266Client"))   //  connection au broker Mqtt  
      {
        Serial.println("connectée au serveur mqtt");  // affiche par l'USB que l'on est connecté
        Mqtt_client.subscribe("gestion");    // indique que l'on ecoute sur le topic gestion
      } 
      else                        // si non 
      {
        int etat=Mqtt_client.state();
        Serial.print("Erreur au niveau : ");  // affiche l'erreur 
        Serial.println(etat);   // info debloquage + ln=retour à la ligne
        M5.Lcd.fillScreen(TFT_BLACK); //efface l'écrant
        M5.Lcd.setCursor(0, 0, 2);   //Curceur en haut à gauche et de taille 2
        M5.Lcd.print("Erreur Mqtt : ");     // affiche du texte
        M5.Lcd.println(etat);   // affiche le type d'erreur sous forme de chiffre
        delay(2000);  //  attend 2000ms=2s
   
      }
      M5.Lcd.fillScreen(TFT_BLACK); //efface l'écran 
    }
     
}





void envoi_message()
{
    /*conversion de int en char*/
  //int num = 1234;
  

  //itoa(num, cstr, 10);

  String msg = "test";
  char envoi[10]="";
  char temp[5];
  msg=String(compt);
  msg.toCharArray(temp,5);
  strcat(envoi,temp);
  strcat(envoi,":");//ajoute une donneee à la fin du char envoi

  
  msg=String(debit);
  msg.toCharArray(temp,5);
  strcat(envoi,temp);
  Serial.println(envoi);

  
//format de l'envoie en Mqtt                "5:0:1.54"
//............................nb_litre : etat_electrovanne : debit sur 10s.........................//
  

  Mqtt_client.publish("envoi/conso", envoi);
}
void menu()
{
  switch(n_menu)
  {
    case 0:
    menu_info();
    break;
    case 1:
    menu_conn();
    break;
  }
}

void menu_info()
{
  M5.Lcd.setCursor(0, 0, 2);
  M5.Lcd.print("Consommation : ");
  M5.Lcd.println(compt);
  M5.Lcd.print("Electrovanne : ");
  M5.Lcd.println(Etat_electrovanne);
  M5.Lcd.print("Debit : ");
  M5.Lcd.println(debit);
  //M5.Lcd.drawString("X",40,120,2);
}

void menu_conn()
{
  M5.Lcd.setCursor(0, 0, 2);
  M5.Lcd.print("WiFi : ");
  M5.Lcd.println("OK");
  M5.Lcd.print("Mqtt : ");
  M5.Lcd.println("OK");
  M5.Lcd.print("Ip : ");
  M5.Lcd.println(WiFi.localIP());
  
  M5.Lcd.print("Mac : ");
  M5.Lcd.println(WiFi.macAddress());

}

void ouverture_electrovanne()
{
  digitalWrite(electrovanne, LOW);  //signal continu = 0V  
  Etat_electrovanne = 1;    // 1 :L'electrovanne est ouverte
}






void fermeture_electrovanne()
{
  digitalWrite(electrovanne, HIGH);   //signal continu = 3,3V
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
