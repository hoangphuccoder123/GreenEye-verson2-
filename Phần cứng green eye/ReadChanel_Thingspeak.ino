#include <ESP8266WiFi.h>
#include "secrets.h"
#include "ThingSpeak.h" // always include thingspeak header file after other header files and custom macros

# define LED_voco 5
# define LED_huuco 16

char ssid[] = "🙈";   // your network SSID (name) 
char pass[] = "01122020";   // your network password
int keyIndex = 0;            // your network key Index number (needed only for WEP)
WiFiClient  client;

// Weather station channel details
unsigned long Chanel_ID = 2799172;
unsigned int dataread = 0;
unsigned int FieldNumber = 1;

// Counting channel details
unsigned long counterChannelNumber = 1;
const char * myCounterReadAPIKey = "8XFMOJ6DXG3TSBKG";
unsigned int counterFieldNumber = 1; 

void setup() {
  Serial.begin(115200);  // Initialize serial
  while (!Serial) {
    ; // wait for serial port to connect. Needed for Leonardo native USB port only
  }
  pinMode(LED_voco,OUTPUT);
  pinMode(LED_huuco,OUTPUT);

  WiFi.mode(WIFI_STA); 
  ThingSpeak.begin(client);  // Initialize ThingSpeak
}

void loop() {

  int statusCode = 0;
  
  // Connect or reconnect to WiFi
  if(WiFi.status() != WL_CONNECTED){
    Serial.print("Attempting to connect to SSID: ");
    Serial.println(SECRET_SSID);
    while(WiFi.status() != WL_CONNECTED){
      WiFi.begin(ssid, pass); // Connect to WPA/WPA2 network. Change this line if using open or WEP network
      Serial.print(".");
      delay(5000);     
    } 
    Serial.println("\nConnected");
  }

  // Read in field 1 of the public channel recording the temperature
  float dataread = ThingSpeak.readFloatField(Chanel_ID, FieldNumber);  

  // Check the status of the read operation to see if it was successful
  statusCode = ThingSpeak.getLastReadStatus();
  if(statusCode == 200){+
    Serial.println("Read data ok: " + String(dataread));
  }
  else{
    Serial.println("Problem reading channel. HTTP error code " + String(statusCode)); 
  }
  if (dataread == 2)
    {
      digitalWrite(LED_voco, HIGH);
      digitalWrite(LED_huuco, LOW);
    }
  else if (dataread == 1)
    {
      digitalWrite(LED_voco, LOW);
      digitalWrite(LED_huuco, HIGH);
    }
  else
    {
      digitalWrite(LED_voco, LOW);
      digitalWrite(LED_huuco, LOW);
    }
  delay(1000); // No need to read the temperature too often.

}
