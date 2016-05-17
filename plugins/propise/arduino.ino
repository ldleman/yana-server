// Demo using DHCP and DNS to perform a web client request.
// 2011-06-08 <jc@wippler.nl> http://opensource.org/licenses/mit-license.php

#include <EtherCard.h>
#include <DHT.h>

//Pin du capteur humiditÃ© + temperature
#define DHT11Pin 2
//Pin du capteur de luminositÃ©
#define LIGHT_PIN 0
//Pin du capteur de prÃ©sence
#define PRESENCE_PIN 3
//Pin du capteur de prÃ©sence
#define SOUND_PIN 4
//Pin de la led temoin
#define LED_PIN 5
//L'Ã©tat de presence/non prÃ©sence doit Ãªtre maintenu mini 5 sc pour etre pris en compte
#define SOUND_DELAY 5000 
//Les infos sont envoyÃ©es toutes les 5sc
#define SEND_INTERVAL 20000
//L'Ã©tat de presence/non prÃ©sence doit Ãªtre maintenu mini 5 sc pour etre pris en compte
#define PRESENCE_DELAY 5000 



float humidity = 0;
float celsius = 0;
float light = 0;
float lightPourcent = 0;
int realsound = 0;
//Variable de stokage du temps du dernier envois
unsigned long lastSending = 0;
//Initialisation de la présence a 0
int lastPresenceState = LOW;
//Initialisation du dernier temps de changement de l'etat de presence
unsigned long lastPresenceStateTime = 0;
//Initialisation du son a 0
int lastSoundState = LOW;
//Initialisation du dernier temps de changement de l'etat de son
unsigned long lastSoundStateTime = 0;


// ethernet interface mac address, must be unique on the LAN
static byte mymac[] = { 0x74,0x69,0x69,0x2D,0x30,0x31 };

byte Ethernet::buffer[700];
static uint32_t timer;

const char website[] PROGMEM = "192.168.0.13";

// called when the client request is complete
static void my_callback (byte status, word off, word len) {
  Ethernet::buffer[off+len] = 0;
  Serial.print((const char*) Ethernet::buffer + off);
  
}

DHT dht(DHT11Pin, DHT11);

void setup () {
  Serial.begin(9600);


  pinMode(PRESENCE_PIN, INPUT);    
  pinMode(LIGHT_PIN, INPUT);
  pinMode(SOUND_PIN, INPUT);
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN,LOW);
  Serial.println("Demarrage de P.R.O.P.I.S.E");
  delay(3000);
  digitalWrite(LED_PIN,HIGH);
  Serial.println("Rruuullleez !");
  
  Serial.println("\n[webClient]");

  if (ether.begin(sizeof Ethernet::buffer, mymac) == 0)
    Serial.println( "Failed to access Ethernet controller");
  if (!ether.dhcpSetup())
    Serial.println("DHCP failed");

  ether.printIp("IP:  ", ether.myip);
  ether.printIp("GW:  ", ether.gwip); 
  ether.printIp("DNS: ", ether.dnsip); 

  if (!ether.dnsLookup(website))
    Serial.println("DNS failed");
   ether.persistTcpConnection(true);


    ether.hisip[0] = 192;
    ether.hisip[1] = 168;
    ether.hisip[2] = 0;
    ether.hisip[3] = 13;
   
  ether.printIp("SRV: ", ether.hisip);
}

void loop () {
  ether.packetLoop(ether.packetReceive());

  int presence = digitalRead(PRESENCE_PIN);

   if(presence!=lastPresenceState && ((millis()-lastPresenceStateTime) > PRESENCE_DELAY)){
      lastPresenceState = presence;
      lastPresenceStateTime = millis();
   }

    int sound = digitalRead(SOUND_PIN);
   //Serial.println(sound);
   if(sound!=lastSoundState && ((millis()-lastSoundStateTime) > SOUND_DELAY)){
      lastSoundState = sound;
      lastSoundStateTime = millis();
   }

if(millis()-lastSending > SEND_INTERVAL){
      digitalWrite(LED_PIN,LOW);
      Serial.println("Stockage des informations");
      humidity = dht.readHumidity();
      celsius = dht.readTemperature();
      light = analogRead(LIGHT_PIN);
    
      //Conversion resistance lumineuse en pourcentage : sur mon capteur : 955 = noir = 0% , 25 = trÃ¨s lumineux = 100%
      light = light - 25;
      lightPourcent = 100 - (( light * 100) /955);
    
      //Inversion de l'etat son pour plus de logique
      realsound = lastSoundState==1?0:1;
      
      Serial.print("- Humidite: "); Serial.println(humidity);
      Serial.print("- Temperature: "); Serial.println(celsius);
      Serial.print("- Lumiere: "); Serial.println(light);
      Serial.print("- Pourcentage: "); Serial.println(lightPourcent);
      Serial.print("- Presence: "); Serial.println(lastPresenceState);
      Serial.print("- Son: "); Serial.println(realsound);
    
      
      digitalWrite(LED_PIN,HIGH);
      Serial.print("Envois de la requete avec les parametres : ");
      Serial.print(datastring());
      ether.browseUrl(PSTR("/perso/yana-server/action.php?"), strToChar(datastring()), website, my_callback);
  
      lastSending = millis();
   }

}

static String datastring() {

//humidity,celsius,lightPourcent,lastPresenceState,realsound
  
  String datas = "action=propise_add_data&uid=sonde1&humidity=";
  
   // char strHumidity[20];
    //dtostrf(humidity, 3, 0, strHumidity);
  
  datas += humidity;
  datas += "&temperature=";
  
   // char strCelsius[20];
   // dtostrf(celsius, 3, 2, strCelsius);
  
  datas += celsius;
  datas += "&light=";
  
    //char strLight[20];
  //  dtostrf(lightPourcent, 3, 0, strLight);
  
  datas += lightPourcent;
  datas += "&presence=";

  //  char strPresence[20];
   // dtostrf(lastPresenceState, 1, 0, strPresence);
  
  datas += lastPresenceState;
  datas += "&sound=";
  
   // char strSound[20];
   // dtostrf(realsound, 1, 0, strSound);
   datas += realsound;
  return datas;
}

char* strToChar(String s) {
  unsigned int bufSize = s.length() + 1; //String length + null terminator
  char* ret = new char[bufSize];
  s.toCharArray(ret, bufSize);
  return ret;
}

 char* decimal_string(int num, char* _buf){
    char buf3[3];
    itoa(num/100, _buf, 10);
    strcat(_buf, ".");
    itoa(num%100, buf3, 10);
    strcat(_buf, buf3);
    return _buf;
}
