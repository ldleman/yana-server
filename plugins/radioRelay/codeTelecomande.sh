#!/bin/bash
ERR="\033[1;31m"
NORMAL="\033[0;39m"
INFO="\033[1;34m"
WARN="\033[1;33m"
OK="\033[1;32m"

message="Brancher un récepteur radio 433Mhz sur la broche 1 (wiringPi)

Sur la colonne de broches sur le bord extérieur
Compter 6 broches
5V - 5V - GND - TX - RX - 1 
Si le récepteur n'est pas branché correctement, vous serez averti \n\

Appuyez sur n'importe quel bouton de votre télécomande pour afficher le code

"

whiptail --title "Récupération du code de télécommande CHACON" --msgbox "$message" 0 0
echo -e "$OK Code de la télécommande pris par le raspberry pi: $NORMAL"
echo -e "$ERR Appuyer sur CTRL-C pour sortir du programme $NORMAL"

./chaconGetRemote 1
