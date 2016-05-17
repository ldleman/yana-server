/*

Compile: 
g++ -g -o nerve nerve.cpp -lwiringPi

Run:
cd /var/www/yana-server
sudo chmod +x ./nerve
sudo ./nerve /var/www/yana-server/action.php

*/
 
#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <iostream>
#include <sys/time.h>
#include <sstream>


#define BUTTON_PIN 0

std::string phppath;


std::string longToString(long mylong){
  std::string mystring;
  std::stringstream mystream;
  mystream << mylong;
  return mystream.str();
}

void gpio_change(int pin){
  long state = digitalRead(pin);
   printf( "GPIO %d CHANGED TO %d\n", pin,state);
   std::string command;
   std::string path = "php ";
   path.append(phppath);
   command = path+" GPIO_HAS_CHANGED";
   command.append(" "+longToString(pin));
   command.append(" "+longToString(state));
   printf( "LAUNCH %s\n", command.c_str());
   system(command.c_str());
}

void gpio_0() {
   gpio_change(0);
}
void gpio_1() {
   gpio_change(1);
}
void gpio_2() {
   gpio_change(2);
}
void gpio_3() {
   gpio_change(3);
}
void gpio_4() {
   gpio_change(4);
}
void gpio_5() {
   gpio_change(5);
}
void gpio_6() {
   gpio_change(6);
}
void gpio_7() {
   gpio_change(7);
}
void gpio_8() {
   gpio_change(8);
}
void gpio_9() {
   gpio_change(9);
}
void gpio_10() {
   gpio_change(10);
}
void gpio_11() {
   gpio_change(11);
}
void gpio_12() {
   gpio_change(12);
}
void gpio_13() {
   gpio_change(13);
}
void gpio_14() {
   gpio_change(14);
}
void gpio_15() {
   gpio_change(15);
}
void gpio_16() {
   gpio_change(16);
}
void gpio_17() {
   gpio_change(17);
}
void gpio_18() {
   gpio_change(18);
}
void gpio_19() {
   gpio_change(19);
}
void gpio_20() {
   gpio_change(20);
}
void gpio_21() {
   gpio_change(21);
}
void gpio_22() {
   gpio_change(22);
}
void gpio_23() {
   gpio_change(23);
}
void gpio_24() {
   gpio_change(24);
}
void gpio_25() {
   gpio_change(25);
}

void gpio_26() {
   gpio_change(26);
}
void gpio_27() {
   gpio_change(27);
}
void gpio_28() {
   gpio_change(28);
}
void gpio_29() {
   gpio_change(29);
}



void logline(std::string a){
	std::cout << a << std::endl;
}
	



// -------------------------------------------------------------------------
// main
int main(int argc, char** argv) {

	logline("Launch nerves...");
	phppath = argv[1];
  // sets up the wiringPi library
  if (wiringPiSetup () < 0) {
      fprintf (stderr, "Unable to setup wiringPi: %s\n", strerror (errno));
      return 1;
  }


  //for (long i=0;i<40;i++)
 // {
    
    if ( wiringPiISR (0, INT_EDGE_BOTH, &gpio_0) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
    if ( wiringPiISR (1, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
    if ( wiringPiISR (2, INT_EDGE_BOTH, &gpio_2) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (3, INT_EDGE_BOTH, &gpio_3) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (4, INT_EDGE_BOTH, &gpio_4) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (5, INT_EDGE_BOTH, &gpio_5) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (6, INT_EDGE_BOTH, &gpio_6) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (7, INT_EDGE_BOTH, &gpio_7) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     /*if ( wiringPiISR (8, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (9, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (10, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (11, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (12, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (13, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (14, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (15, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (16, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (17, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (18, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (19, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (20, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (21, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (22, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
     if ( wiringPiISR (23, INT_EDGE_BOTH, &gpio_1) < 0 )  fprintf (stderr, "Unable to setup ISR: %s\n", strerror (errno));
      */ 
    
 // }

  logline("Nerves ready, waiting life.");
  while(1){
	  
  }

  return 0;
}
