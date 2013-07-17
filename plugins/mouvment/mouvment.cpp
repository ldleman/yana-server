#include <wiringPi.h>
#include <iostream>
#include <stdio.h>
#include <sys/time.h>
#include <stdlib.h>
#include <sstream>
#include <vector>
#include <string>

using namespace std;

//g++ mouvment.cpp -o mouvment -lwiringPi

int pin = 0;

void log(string a){
	//Décommenter pour avoir les logs
	cout << a << endl;
}


string longToString(long mylong){
    string mystring;
    stringstream mystream;
    mystream << mylong;
    return mystream.str();
}

vector<string> explode(const string& str)
{
    istringstream split(str); // flux d'exatraction sur un std::string
    vector<string> tokens;
    for (string each; std::getline(split, each, ','); tokens.push_back(each));
    return tokens;
}

int main (int argc, char** argv)
{
	
	string command;
	string path = "php ";
	path.append(argv[1]);

	log("Demarrage du programme");
    log("Chemin du programme de sortie: ");
    cout << argv[1] << endl;
    log("Pin(s) GPIO configure(s) en entree :");
    cout << argv[2] << endl;

	vector<string> pins= explode(argv[2]);

	//Si on ne trouve pas la librairie wiringPI, on arrête l'execution
    if(wiringPiSetup() == -1)
    {
        log("Librairie Wiring PI introuvable, veuillez lier cette librairie...");
        return -1;
    }
    pinMode(pin, INPUT);
	

	for(;;)
    {
        for(int unsigned i=0;i<pins.size();i++){
            pin = atoi(pins[i].c_str());
            cout << "Pin : " <<  pins[i] << ", Etat:" << longToString(digitalRead(pin)) << endl;
    		command = path+" mouvment_set_state "+pins[i];
    		command.append(" "+longToString(digitalRead(pin)));
            cout << "Execution commande : " << command << endl;
    		system(command.c_str());
         } 
         delay(500);
    }

	
}

