#include <M5StickC.h>
#include <WiFi.h>
#include <PubSubClient.h>

//#include <SPI.h>


// wifi
const char* ssid     = "Livebox-3002";
const char* password = "aSzy24ZzWm5xrKrumG"; 

// mqtt
const char* mqtt_host = "90.3.16.36";
const int mqtt_port = 1883;

//const char* mqttUser = ""; 
//const char* mqttPassword = "";





WiFiClient wifi_client;  //objet pour le wifi
PubSubClient Mqtt_client(mqtt_host, mqtt_port, nullptr, wifi_client);  //objet pour le Mqtt

void connection_Mqtt();
int n_menu=0;
int compteur_eau = 26;  // pin D2
int electrovanne = 5;  // pin D1
int compt=0;       //contient le nombre de litre/impulsion 
int Etat_electrovanne=0;                  // 0=fermée  1=ouvert
int dernier_litre=0;

void ICACHE_RAM_ATTR nb_impulsion(void);   // ICACHE_RAM_ATTR permet de charger attachInterrupt dans la RAM // permet de compter le nombre d'impulsion donc de litre

void ouverture_electrovanne();
void fermeture_electrovanne();

void menu();
void menu_info(); 
void menu_conn();


void envoi_message();

float calcul_debit(int litre, float temps);
float debit=0;
float dernier_debit=0;

int nb_debit=0;
unsigned long duree;
unsigned long derniere_duree;
//U8G2_SH1107_64X128_1_4W_HW_SPI u8g2(U8G2_R1, 14, 27, 33);
//xTaskCreatePinnedToCore(compositeCore, "c", 2048, NULL, 1, NULL, 0);
String message = "";




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


  String msgString = String(message_buff);   //convertie le message en string 
  Serial.println(msgString);   //affiche le message

    
    M5.Lcd.setCursor(40, 30);
    M5.Lcd.setTextSize(1);
    if (msgString == "1")
    {
      ouverture_electrovanne();  
      envoi_message();        //envoi un message de confirmation
      M5.Lcd.setTextColor(WHITE, BLUE);
      M5.Lcd.fillScreen(BLUE);
      M5.Lcd.println("circuit ferme"); 
    }
    if (msgString == "0")
    {
      fermeture_electrovanne();
      envoi_message();   //envoi un message de confirmation
      M5.Lcd.setTextColor(WHITE, RED);
      M5.Lcd.fillScreen(RED);
      M5.Lcd.println("circuit ouvert");
    }
    delay(1000);
    M5.Lcd.fillScreen(TFT_BLACK); //efface l'écrant
    M5.Lcd.setTextColor(WHITE, BLACK);
    menu();
    
  }





void setup() 
{
  M5.begin();
  M5.Lcd.fillScreen(TFT_BLACK);
  M5.Lcd.setTextColor(WHITE,BLACK);
  M5.Lcd.setRotation(3);  //format paysage
  Serial.begin(115200);    // communique avec 115200 baud (vitesse de communication)
  pinMode(compteur_eau, INPUT);   //compteur d'eau brancher sur la pin D2 
  pinMode(electrovanne, OUTPUT);    //electrovanne brancher sur la pin D1
  attachInterrupt(digitalPinToInterrupt(compteur_eau),nb_impulsion,RISING);   //interruption lors qu'il y a front montant
  
  
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) 
  {
    M5.Lcd.setCursor(0, 0);
    Serial.print("Connection au WiFi : ");
    Serial.println(WiFi.status());
    M5.Lcd.print("Connection au WiFi : ");
    M5.Lcd.println(WiFi.status());
    delay(500);
  }
  
  M5.Lcd.fillScreen(TFT_BLACK);
  Serial.println("Connectée au réseau WiFi");
  //M5.Lcd.println()
  
  //M5.Lcd.drawChar(0, 0, chaine[0], 0xF800, 0,255,160);
 


  
  //M5.Lcd.drawJpg("/IMG_20191213_120859 bis.jpg",  100, 0, 0);
  //M5.Lcd.println("hello world !!!! ");
  //M5.Lcd.fillScreen(TFT_BLACK);
  //M5.Lcd.print(WiFi.localIP());
  
  
  //client.setServer(mqttServer, mqttPort);   //definition du server Mqtt
  Mqtt_client.setCallback(callback);   //defini la fonction de retour

/** connection au broker Mqtt**/
  
  connection_Mqtt();
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
     connection_Mqtt();
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
       while (!Mqtt_client.connected()) // tant que le client n'est pas connecté
    {
      if (Mqtt_client.connect("ESP8266Client"))   //  connection au broker Mqtt  
      {
        Serial.println("connectée au serveur mqtt");  // affiche par l'USB que l'on est connecté
        Mqtt_client.subscribe("gestion");
      } 
      else                        // si non 
      {
        int etat=Mqtt_client.state();
        Serial.print("Erreur au niveau : ");  // affiche l'erreur 
        Serial.println(etat);   // info debloquage + ln=retour à la ligne
        M5.Lcd.setCursor(0, 0, 2);
        M5.Lcd.print("Erreur Mqtt : ");
        M5.Lcd.println(etat);
        delay(2000);  //  attend 2000ms=2s
      } 
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
  msg = String(Etat_electrovanne);
  msg.toCharArray(temp,5);
  
  strcat(envoi,temp);
  msg=String(debit);
  msg.toCharArray(temp,5);
  strcat(envoi,":");  //ajoute une donneee à la fin du char envoi
  strcat(envoi,temp);
  Serial.println(envoi);

  
//format de l'envoie en Mqtt                "5:0:1.54"
//............................nb_litre : etat_electrovanne : debit sur 10s.........................//
  

  Mqtt_client.publish("compteur_connecte/conso", envoi);
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
  byte mac[6];
  WiFi.macAddress(mac);
  M5.Lcd.print("Mac : ");
  M5.Lcd.print(mac[5],HEX);
  M5.Lcd.print(":");
  M5.Lcd.print(mac[4],HEX);
  M5.Lcd.print(":");
  M5.Lcd.print(mac[3],HEX);
  M5.Lcd.print(":");
  M5.Lcd.print(mac[2],HEX);
  M5.Lcd.print(":");
  M5.Lcd.print(mac[1],HEX);
  M5.Lcd.print(":");
  M5.Lcd.println(mac[0],HEX);
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
