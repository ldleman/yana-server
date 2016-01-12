/*
Ce programme est basé sur RadioReception de Valentin Carruesco
Il permet de récupérer un code radio chacon dans le format de RCsend
g++ HEreceive.cpp -o HEreceive -lwiringPi

Cette page récupere les informations du signal radio recu par le raspberry PI et execute une page PHP
en lui fournissant tout les paramêtres.

Vous pouvez compiler cette source via la commande :
    g++ radioReception.cpp -o radioReception -lwiringPi
    
    N'oubliez pas d'installer auparavant la librairie wiring pi ainsi que l'essentiel des paquets pour compiler

Vous pouvez lancer le programme via la commande :
    sudo chmod 777 radioReception
    ./radioReception /var/www/radioReception/radioReception.php  7

    Les deux parametres de fin étant le chemin vers le PHP a appeller, et le numéro wiringPi du PIN relié au récepteur RF 433 mhz
    
@author : Valentin CARRUESCO (idleman@idleman.fr)
@contributors : Yann PONSARD, Jimmy LALANDE
@webPage : http://blog.idleman.fr
@references & Libraries: https://projects.drogon.net/raspberry-pi/wiringpi/, http://playground.arduino.cc/Code/HomeEasy
@licence : CC by sa (http://creativecommons.org/licenses/by-sa/3.0/fr/)
RadioPi de Valentin CARRUESCO (Idleman) est mis à disposition selon les termes de la 
licence Creative Commons Attribution - Partage dans les Mêmes Conditions 3.0 France.
Les autorisations au-delà du champ de cette licence peuvent être obtenues à idleman@idleman.fr.
*/


#include <wiringPi.h>
#include <iostream>
#include <stdio.h>
#include <sys/time.h>
#include <time.h>
#include <stdlib.h>
#include <sched.h>
#include <sstream>

using namespace std;

//initialisation du pin de reception
int pin;
int received_code;
int ok_code = 0;
int wait_cycles = 1;

//Fonction de log
void log(string a){
    //Décommenter pour avoir les logs
    //cout << a << endl;
}

//Fonction de conversion long vers string
string longToString(long mylong){
    string mystring;
    stringstream mystream;
    mystream << mylong;
    return mystream.str();
}

//Fonction de passage du programme en temps réel (car la reception se joue a la micro seconde près)
void scheduler_realtime() {
    struct sched_param p;
    p.__sched_priority = sched_get_priority_max(SCHED_RR);
    if( sched_setscheduler( 0, SCHED_RR, &p ) == -1 ) {
        perror("Failed to switch to realtime scheduler.");
    }
}

//Fonction de remise du programme en temps standard
void scheduler_standard() {
    struct sched_param p;
    p.__sched_priority = 0;
    if( sched_setscheduler( 0, SCHED_OTHER, &p ) == -1 ) {
        perror("Failed to switch to normal scheduler.");
    }
}

//Recuperation du temp (en micro secondes) d'une pulsation
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

//Programme principal
int main (int argc, char** argv)
{
    //On passe en temps réel
    scheduler_realtime();
    
    //on récupere l'argument 2, qui est le numéro de Pin GPIO auquel est connecté le recepteur radio
    pin = atoi(argv[1]);
    //Si on ne trouve pas la librairie wiringPI, on arrête l'execution
    if(wiringPiSetup() == -1)
    {
        log("WiringPI:Notfounded");
        return -1;
    }else{
        log("WiringPI:OK");
    }
    pinMode(pin, INPUT);
    log("GPIO configured");
    log("Waiting for signal ...");
    

       //We received GPIO State to see if something is happening
    for (int timer = 0; timer < 50; ++timer)
    {
        received_code = digitalRead(pin);
      if (received_code == 1) { ok_code++; } //If PIN IS HIGH add to ok_code
      //printf("%i",digitalRead(pin));
      delay(10);
  }

    if (ok_code == 0) //If the PIN was never on HIGH we assume there was a problem
    {
        printf("Wrong GPIO\n");
        exit(1);
    }
    else //If test1 PASS
    {      



    //On boucle pour ecouter les signaux

        for(int cycles=0;cycles < wait_cycles;cycles++)
        {
            
            int i = 0;
            unsigned long t = 0;
        //avant dernier byte reçu
            int prevBit = 0;
        //dernier byte reçu
            int bit = 0;

        //mise a zero de l'idenfiant télécommande
            unsigned long sender = 0;
        //mise a zero du groupe
            bool group=false;
        //mise a zero de l'etat on/off
            bool on =false;
        //mise a zero de l'idenfiant de la rangée de bouton
            unsigned long recipient = 0;


            t = pulseIn(pin, LOW, 1000000);

        //Verrou 1
            while((t < 2700 || t > 2800)){
                t = pulseIn(pin, LOW,1000000);
            }
            log("Lock 1 detected");
        // données
            while(i < 64)
            {
                t = pulseIn(pin, LOW, 1000000);
            //cout << "t = " << t << endl;

            //Définition du bit (0 ou 1)
                if(t > 180 && t < 420)
                {
                    bit = 0;
                }

                else if(t > 1280 && t < 1480)
                {
                    bit = 1;
                }
                else
                {
                    i = 0;
                    break;
                }


                if(i % 2 == 1)
                {
                    if((prevBit ^ bit) == 0)
                    {
                    // doit être 01 ou 10,,pas 00 ou 11 sinon ou coupe la detection, c'est un parasite
                        i = 0;
                        break;
                    }

                    if(i < 53)
                    {
                    // les 26 premiers (0-25) bits sont l'identifiants de la télécommande
                        sender <<= 1;
                        sender |= prevBit;
                    }      
                    else if(i == 53)
                    {
                    // le 26em bit est le bit de groupe
                        group = prevBit;
                    }
                    else if(i == 55)
                    {
                    // le 27em bit est le bit d'etat (on/off)
                        on = prevBit;
                    }

                    else
                    {
                    // les 4 derniers bits (28-32) sont l'identifiant de la rangée de bouton
                        recipient <<= 1;
                        recipient |= prevBit;
                    }
                }

                prevBit = bit;
                ++i;
            }

    //Si les données ont bien été détéctées
            if(i>0){

                log("------------------------------");
                log("Code de la télécommande");
        //on construit la commande qui vas envoyer les parametres au PHP
                cout << sender << endl;
                exit(0);

            }else{
                log("Aucun code detecté");
            }

        }

        scheduler_standard();
    }
}

