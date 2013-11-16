#!/bin/bash

#sudo wget https://raw.github.com/ldleman/yana-server/master/install.sh && sudo chmod +x install.sh && sudo ./install.sh
## Description: Script d'installation pour YANA
## Author:Sarrailh Remi
## Email:maditnerd@gmail.com
## License:http://www.tldrlegal.com/l/Gplv3

# Options
wait=2 #Attend 2 secondes avant chaque action
check=1 #Verifie connexion internet/etat cache apt
dir=yana-server #Dossier ou sera installe yana

#Options dev
repo=ldleman #Repo utilisee
branch=master #Branche utilisee

web_right=755 #Droits pour l'utilisateur web
web_user=www-data #Utilisateur web







############### DEPENDANCES

########## BIBLIOTHEQUE LAZASS (version FR Standalone Abridged Unattended) ###################
##! LAZASS LIBRARY 1.8
##* Description: Library for installation/configuration scripts
##* Author:Sarrailh Remi   
##* License:http://www.tldrlegal.com/l/Gplv3
##* Site web:http://maditnerd.github.io/lazass/
###****
VER="1.8-FR-SA-AB-UN"
AUTHOR="Sarrailh Remi"
LICENSE="Gplv3"
###################
##!! DISPLAY FUNCTIONS
#########################################################
##> These functions display text without doing nothing else
#########################################################

###############
##!!! COLOR    
###############
OK="\\033[1;32m"      ##* $OK      -> GREEN
NORMAL="\\033[0;39m"  ##* $NORMAL  -> WHITE
ERR="\\033[1;31m"     ##* $ERR     -> RED
INFO="\\033[1;34m"    ##* $INFO    -> BLUE
WARN="\\033[1;33m"    ##* $WARN    -> YELLOW
PICOLOR="\\033[1;35m" ##* $PICOLOR ->PINK

##############
##!!! TEXT    
##############
#>* $1 TEXT
#>* $2 COLOR (optional)

##!!!! nlbecho() : Text with no line break
nlbecho() {
	echo -ne $2
	echo -ne "$1 "
	echo -ne $NORMAL
}

##!!!! colecho() : Colored text
colecho() {
	echo -ne $2
	echo $1
	echo -ne $NORMAL
}

##!!!! listecho() : Make a List item with ->
listecho() {
	nlbecho "->" $OK 
	echo $1
}

##########################
##!!! INFORMATION MINIBOX
##########################

##!!!! ok() : GREEN [OK]
function ok() {
	echo -ne " ["
	echo -ne $OK
	echo -ne "OK"
	echo -ne $NORMAL
	echo "]"
}

##!!!! skip() BLUE [SKIPPED]
function skip() {
	echo -ne " ["
	echo -ne $INFO
	echo -ne "DEJA FAIT"
	echo -ne $NORMAL
	echo "]"
}

##!!!! warning() YELLOW [WARNING]
function warning() {
	echo -ne " ["
	echo -ne $WARN
	echo -ne "ATTENTION"
	echo -ne $NORMAL
	echo "]"
}

##!!!! error() RED [ERROR]
function error() {
	echo -ne " ["
	echo -ne $ERR
	echo -ne "ERREUR"
	echo -ne $NORMAL
	echo "]"
}

#################
##!!! LINE BREAK 
#################

##!!!! jumpline() Simple Line break
jumpline() {
	echo ""
}

##!!!! line_break() Line break with a line
line_break() {
	echo -e $INFO"____________________________________"
	echo -e $NORMAL
}

#########
##!!!  BOX
#########
#>* $1 TEXT

##!!!! description() : Create a description box, Use it to describe your SCRIPT
description() {
	echo -ne $WARN 
	echo "LAZASS INSTALLER $VER par $AUTHOR - $LICENSE"
	echo "__________________________________"
	echo "__________________________________"
	echo "$1"
	echo "__________________________________"
	echo "__________________________________"
	echo -ne $NORMAL
}

##!!!! messagebox() : Create a messagebox, Use it to describe an ACTION
messagebox() { 
echo -e $INFO"____________________________________"
echo $1
line_break
}

##!!!! error_box() : Create an error box to show an CRITICAL ERROR
##>* $1 TEXT/ERROR CODE
error_box() {
	echo -ne $ERR
	echo "--- CODE D'ERREUR: $1 ---"
	echo -ne $NORMAL
}

##!!!! console() Create a console box use it to show COMMAND OUTPUT
console() {
	echo -ne $OK
	echo "/////////////////////"
	echo -ne $NORMAL
}


##!!!! thank() : Thanks message, use it at the END OF SCRIPT
function thank () {
	if [[ $noreset != 1 ]]
		then
		echo ""
		echo -ne $INFO
		echo "^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^"
		echo "Amusez vous bien les makers !"
		echo "------------------------------"
		echo -ne $NORMAL
		exit 0
	fi
}


###################
##!! ERRORS HANDLER 
#################################################
##> Theses functions handles errors          
#################################################

##!!!! verify_root() : Verify if you have root permissions
verify_root() {
	if [ $(id -u) -ne 0 ]; then
		error_box
		echo "Oups vous avez oublie de vous mettre en root!"
		echo "Essayez de faire 'sudo $0'"
		echo -ne $NORMAL
		error_box
		echo -ne $NORMAL
		exit 1
	fi
}

##!!!! aptget_error() : Handle APT error (launched when using installer)
aptget_error() {
	apterror=$1
	case $apterror in 
		100)
echo -ne $ERR
echo "" 
error_box $apterror
echo "DPKG A L'AIR BLOQUE! !"
listecho "AVEZ VOUS LANCE UNE AUTRE INSTALLATION (qui ne serait pas finit) ?"
listecho "AVEZ VOUS BIDOUILLE /etc/apt/sources.list ?"
listecho "Un petit apt-get update de bon matin ca fait pas de mal"
echo "SI CE N'EST PAS LE CAS, JE PEUX LE BRUTALISER UN PEU!"
error_box $apterror
read -r -p "Le deverouiller ? [Y/n] " response
case $response in
	[yY])
rm -vf /var/lib/dpkg/lock
rm -vf /var/cache/apt/archives/lock
echo "RELANCEMENT DE L'INSTALLATION..."
sleep 2
$0
;;
*)
echo ""
error_box $apterror
echo "L'INSTALLATION A ECHOUE!!!"
echo "Vous aller devoir reparer APT-GET par vous meme avant de reessayer! :-("
	error_box $apterror
	exit 1
	;;
esac
;;
0)
echo "OK"
;;

1)
error_box $apterror
echo "Installation Annulee !"
error_box $apterror
echo -ne $NORMAL
read
exit 1
;;

*)
error_box $apterror
echo "ERREUR APT-GET INCONNU!!!"
echo "Si tu peux m'envoyer le code d'erreur / les details a maditnerd@gmail.com, ca m'aiderait bien!"
read -r -p "Arreter l'installation ? [Y/n]" response
error_box $apterror
echo -ne $NORMAL
case $response in
	[yY])
exit 1
;;
esac
;;
esac

}

#########################
##!! Get Infos         
###########################################
##> Get informations from the system      
###########################################

##!!!! firstip() : Return First IP
firstip() {
	echo $(hostname -I |cut -f1 -d' ')
}

##!!!! pint_test() : Ping a website and tell if you are (it is) online or not
##>* $1: address to ping
ping_test() {
	messagebox "Connexion internet"
	console
	ping -c1 $1 && internet=1 || internet=0
	console
	if [[ $internet == 1 ]]
		then
		echo -ne "INTERNET"
		ok
	else
		echo -ne "INTERNET"
		error
		echo -ne $ERR
		echo "Connexion internet introuvable, le script va s'arreter ..."
		echo -ne $NORMAL
		read
		exit 1
	fi
}

#######################
##!! Actions functions
#############################################
##> This functions performs complex actions  
#############################################

#########################
##!!! UNATTENDED ACTIONS  
#########################

##!!!! updater() : Update APT
updater() {
	ping_test "google.com"
	messagebox "Verification du cache APT!"
	console
	apt-get -q -y update
	aptget_error $?
	console
}

##!!!! installer() Install a packet (with error handling)
##>* $1 package (apt-get install package)
installer() {
	messagebox "INSTALLATION DE: $1"
	dpkg -s "$1"|grep "Status: install ok installed" > /dev/null 2>&1
	#IS THE PACKAGE ALREADY INSTALLED ?
	if [[ $? == 1 ]]
		then
		console
		apt-get -q -y install "$1"
		aptget_error $?
		console
		dpkg -s "$1"|grep "Status: install ok installed" > /dev/null 2>&1
			#WAS THE PACKAGE INSTALLED ?
			if [[ $? == 0 ]]
				then
				echo -ne "RESULTAT: $1"
				ok
			else
				echo -ne "RESULTAT: $1"
				error
				read
			fi
		else
			echo -ne "RESULTAT: $1"
			skip
		fi
	}

##!!!! gitcloner() : Git Cloner
##>* $1 Program name (for showing it)
##>* $2 Repo url (ex: http://github.com/nameofuser/nameofrepo)
##>* $3 Name of the directory where the repo will be cloned
##>* $4 Branch of the repo
##>* If the directory exists try to update it
yanacloner() {
	messagebox "Clone $1"
	console
	git clone -b $branch $2 $3
	giterror=$?
	console
	
	if [[ $giterror == 128 ]]
		then
		line_break
		nlbecho "DEJA CLONE!"
		skip
		line_break
		cd $3
		git status -sb |grep "master"|grep "behind" > /dev/null 2>&1
		if [[ $? == 0 ]]
			then
			TIMESTAMP=$(date +%H%M%S)'_'$(date +%d%m%y)
			messagebox "Mise a jour de $1"
			colecho "Une mise a jour est disponible pour YANA!" $WARN
			line_break
			colecho "La configuration et les plugins ajoutes ne seront pas supprime!" $ERR
			colecho "Mais toutes les modifications faites au coeur de yana le seront par contre!" $ERR
			jumpline
			dir_back=$dir-$TIMESTAMP
			colecho "Pas de panique! votre version sera sauvegarde dans /root/$dir_back" $WARN
			line_break
			continue_prompt

			description "Backup de YANA"
			sleep $wait
			console
			cp -Rv /var/www/$dir /root/$dir_back
			console
			jumpline

			description "Mis à jour"
			git reset --hard origin/master
			git pull
			description "Récuperation de plugins.states.json"
			sleep $wait
			cp /root/$dir_back/plugins/plugins.states.json /var/www/$dir/plugins/plugins.states.json
			
		else
			colecho "Aucune mise à jour disponible" $WARN
		fi
	fi
}

#########################
##!!! INTERACTIVE ACTIONS
#########################

##!!!! continue_prompt() : Ask if the user want to continue the script
##> AVOID USING IT (see issue 1)
continue_prompt() {
	read -r -p "On continue? [O/n] " response
	case $response in
		[yYOo])
echo "On continue..."
;;
*)
echo ""
colecho "Installation arrêtée" $info
echo -ne $NORMAL
exit 1
;;
esac
}


###########################################################


# Programme Principale ############################
clear
verify_root
#Verifie que vous etes bien en root et que vous etes connecte aux interwebs et que votre cache APT est OK
if [[ $check == 1 ]]
	then
	description "Vérifications préalables (vous pouvez les deactiver en modifiant la variable check dans le script)"
	updater
	jumpline
fi

#Message avant installation
description "Installation YANA"
line_break
colecho "Parce qu'il vous aime bien, ce script va installer YANA et ses dépendances (lighttpd/sqlite/php)" $INFO
line_break
sleep $wait
jumpline

#Installe lighttpd sqlite
description "Installation de LIGHTTPD (serveur web) Whhaouuhh !"
sleep $wait

installer "lighttpd"
rm -vf /var/www/index.lighttpd.html

installer "php5-common"
installer "php5-cgi"
installer "php5"

messagebox "Configuration de PHP ! Rock'n'roll !"
lighty-enable-mod fastcgi-php

jumpline
description "Installation de SQLite (base de donnees)"
sleep $wait
installer "sqlite3"
installer "libsqlite3-0"
installer "libsqlite3-dev"
installer "php5-sqlite"

messagebox "On relance lighttpd afin qu'il integre tout ca!"
service lighttpd force-reload

line_break
nlbecho "Vous venez d'installer"
colecho "un serveur WEB avec PHP/SQLITE ! Yeeahh !" $OK
line_break

jumpline

description "Installation de git-core (client pour dépot GIT)"
sleep $wait
installer git-core
jumpline

#Install YANA
description "On clone YANA! dans le dossier /var/www/$dir!"
sleep $wait
yanacloner "YANA Server" "https://github.com/$repo/yana-server.git" "/var/www/$dir" $branch

messagebox "On met à jour les permissions pour notre serveur web"
sleep $wait
chmod -R $web_right /var/www
chown -R $web_user:$web_user /var/www/$dir

#Permissions GPIO
description "Permissions GPIO"
sleep $wait
nlbecho "Nous allons donner les permissions root à"
colecho "YANA" $INFO
colecho "mais uniquement aux programmes dont les sources en CPP sont présentes" $ERR
sleep $wait
messagebox "Recherche et modification des Permissions"
console
unset a i
while IFS= read -r -d $'\0' file; do
	file=$(echo $file |sed 's/.cpp//')
	echo "$file"
	chown root:$web_user $file
	chmod +sx $file
done < <(find /var/www/$dir -name *.cpp -type f -print0)
console
line_break
nlbecho "Permissions GPIO"
ok
line_break

nlbecho "Vous avez installé / mis à jour"
colecho "YANA (You Are Not Alone)" $INFO
ip=$(firstip)
colecho "Rendez vous sur: http://$ip/$dir"
colecho "Pour finir l'installation! :D"
thank
