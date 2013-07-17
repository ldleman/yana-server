#include <wiringPi.h>
#include <iostream>
#include <stdio.h>
#include <sys/time.h>
#include <time.h>
#include <stdlib.h>
#include <sched.h>
#include <sstream>

using namespace std;


int pin = 7;

void log(string a){
	//Décommenter pour avoir les logs
	cout << a << endl;
}

void scheduler_realtime() {

struct sched_param p;
p.__sched_priority = sched_get_priority_max(SCHED_RR);
if( sched_setscheduler( 0, SCHED_RR, &p ) == -1 ) {
perror("Failed to switch to realtime scheduler.");
}
}

void scheduler_standard() {

struct sched_param p;
p.__sched_priority = 0;
if( sched_setscheduler( 0, SCHED_OTHER, &p ) == -1 ) {
perror("Failed to switch to normal scheduler.");
}
}

string longToString(long mylong){
    string mystring;
    stringstream mystream;
    mystream << mylong;
    return mystream.str();
}


int pulseIn(int pin, int level, int timeout)
{


   struct timeval tn, t0, t1;
   long micros;
   gettimeofday(&t0, NULL);
   micros = 0;
   while (digitalRead(pin) != level)
   {
      gettimeofday(&tn, NULL);
      if (tn.tv_sec > t0.tv_sec) micros = 1000000L; else micros = 0;
      micros += (tn.tv_usec - t0.tv_usec);
      if (micros > timeout) return 0;
   }
   gettimeofday(&t1, NULL);
   while (digitalRead(pin) == level)
   {
      gettimeofday(&tn, NULL);
      if (tn.tv_sec > t0.tv_sec) micros = 1000000L; else micros = 0;
      micros = micros + (tn.tv_usec - t0.tv_usec);
      if (micros > timeout) return 0;
   }
   if (tn.tv_sec > t1.tv_sec) micros = 1000000L; else micros = 0;
   micros = micros + (tn.tv_usec - t1.tv_usec);
  
   return micros;
}


int main (int argc, char** argv)
{
//scheduler_realtime();
	string command;
	string path = "php ";
	path.append(argv[1]);
	log("Demarrage du programme");
	pin = atoi(argv[2]);
    if(wiringPiSetup() == -1)
    {
        log("Librairie Wiring PI introuvable, veuillez lier cette librairie...");
        return -1;
    }
    pinMode(pin, INPUT);
	log("Pin GPIO configure en entree");
    log("Attente d'un signal du transmetteur ...");
	for(;;)
    {
    	int i = 0;
		unsigned long t = 0;
		int prevBit = 0;
		int bit = 0;
	    unsigned long temperature = 0;
		unsigned long emiter = 0;
		unsigned long positive = 0;
	    bool group=false;
	    bool on =false;
	    unsigned long recipient = 0;
		command = path+" ";
		t = pulseIn(pin, LOW, 1000000);

		while((t < 2550  || t > 2900)){
			t = pulseIn(pin, LOW,1000000);
		}

		while(i < 24)
		{
			t = pulseIn(pin, LOW, 1000000);
	        if(t > 500  && t < 1500)
			{
				bit = 0;
			}
			
	        else if(t > 2000  && t < 3000)
			{
				bit = 1;
			}
			else
			{
				i = 0;
				cout << "FAIL? = " << t << endl;
				break;
			}

			if(i % 2 == 1)
			{
				if((prevBit ^ bit) == 0)
				{
					i = 0;
					break;
				}

				if(i < 15)
				{
					temperature <<= 1;
					temperature |= prevBit;
				}else if(i == 15){
					
					positive = prevBit;
				}else{
					emiter <<= 1;
					emiter |= prevBit;
				}
			}

			prevBit = bit;
			++i;
	}
    if(i>0){
		log("------------------------------");
		log("Donnees detectees");
		
		cout << "temperature = " << temperature << " C" << endl;
		cout << "positif = " << positive << endl;
		cout << "code sonde = " << emiter << endl;
		
		command.append("UPDATE_ENGINE_STATE ");
		command.append(" "+longToString(emiter));
		command.append(" "+longToString(temperature));
		command.append(" "+longToString(positive));
		
		log("Execution de la commande PHP...");
		log(command.c_str());
		system(command.c_str());
	}else{
		log("Aucune donnee...");
	}
	
    	delay(3000);
    }
//scheduler_standard();
}

