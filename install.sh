#!/bin/bash
# Auteur : Remi Sarrailh (maditnerd)
# Licence : MIT
# Un probleme : https://github.com/ldleman/yana-server/issues
# https://tldrlegal.com/license/mit-license

#############
# Variables
#############

INSTALLVERSION="3.0.6"
ERR="\033[1;31m"
NORMAL="\033[0;39m"
INFO="\033[1;34m"
WARN="\033[1;33m"
OK="\033[1;32m"
IPADDRESS=$(hostname -I)
HOSTNAME=$(cat /etc/hostname)

doInstall=0
#Installer le serveur web (se met à 0 si un autre serveur web est installé)
doInstallWebServer=1
isRoot=0
GlobalError=0
confirmErase=0
copyYana=1
resizeSD=1


################
# Messages GUI
################
# J'ai séparé les messages long du GUI du reste du programme
# Afin quel soit facilement modifiable

installMessage="\
Etapes:
---------------------------
* Renommer le Raspberry Pi
* Redimensionner la carte SD
* Mis à jour
* Terminal en français
------------------------------------
* Configuration du fuseau horaire
* Installation wiringPi (pour gérer les GPIO)
-------------------------------------------
* Copie de Yana Server
* Installation du serveur web
* Création de l'utilisateur
* Permissions du serveur web
* Installation du socket / cron
"

checkMessage="\
Nous allons revérifier toute l'installation mais sans modifier ni yana-server ni la configuration
"

renameMessage="\
Vous pouvez accéder à Yana en utilisant un nom plutôt qu'une adresse IP \n\n\
Pour que cela marche depuis Windows, il faut que les services BONJOUR soit installés \n\
C'est le cas si vous avez SKYPE, ITUNES ou Windows 10 sinon il vous faudra l'installer   \n\
http://support.apple.com/kb/DL999 \n\
\n\
\n\
Example : maison sera accessible sur http://maison.local/

"

saveMessage="\
Vous pouvez sauvegarder yana-server sur une clé USB \n\n\
Ceci sauvegardera /var/www/yana-server dans le dossier yana \n\n\
Si une sauvegarde existe sur la clé, elle sera effacée \n\
si vous voulez conserver une sauvegarde précédente renommer le dossier \n\
"

restoreMessage="\
Vous pouvez revenir à un état précédent de yana depuis une clé USB \n\n\
Ceci effacera /var/www/yana-server et le replacera par celui \n\
dans le dossier sur la clé USB /yana
"

resizeSDCardMessage="\
Voulez vous redimensionner la carte SD de votre Raspberry Pi ?

Ceci sera fait au prochain redémarrage.
"

# Message d'erreurs

noInternetMessage="\
Je n'arrive pas à me connecter à github.com \n\
Voici votre adresse IP: $IPADDRESS\
"

ApacheMessage="\
Yana utilise lighttpd comme serveur web par défaut\n\
Il semblerait que Apache (un autre serveur web) soit déjà installé...\n\n\

Voulez vous quand même installer lighttpd ? \n\
"
nginxMessage="\
Yana utilise lighttpd comme serveur web par défaut\n\
Il semblerait que nginx (un autre serveur web) soit déjà installé...\n\n\

Voulez vous quand même installer lighttpd ? \n\
"

yanaMessage="\
Yana semble avoir déjà été copié.\n\
Voulez vous que je le supprimer et que je le réinstalle ?\
"

localeMessage="\
Je n'ai pas réussi à mettre le terminal en français\n\
Pour autant, ceci n'aura aucune incidence sur la suite de l'installation\n\n\
Voici le message d'erreur:\
"

aptGetErrorMessage="\
Le gestionnaire de paquet apt-get est HS\n\
* Soit celui-ci a été interrompu\n\
* Soit il est en cours d'utilisation par un autre programme\n\
Supprimer le fichier de verrou est probablement la solution\n\n\
Voici le message d'erreur:\n\
"

gitErrorMessage="\
Impossible de récupérer le code source avec git\n\
Cela peut être du à un problème du coté de github\n\
Vous pouvez vérifier cela sur https://status.github.com/\n\n\
Voici le message d'erreur:\n\
"

wiringPiErrorMessage="\
Impossible de compiler wiringPi\n\
Voici le message d'erreur:\n\
"

lighttpdErrorMessage="\
Le serveur web n'a pas réussi à se redémarrer correctement\n\
Voici le message d'erreur:\n\
"

#Un joli logo ascii sans avoir à installer un programme pour ça
yanaLogo(){
clear
echo -ne $INFO

cat<<EOF                                                                           
██╗   ██╗ █████╗ ███╗   ██╗ █████╗        ████████╗
╚██╗ ██╔╝██╔══██╗████╗  ██║██╔══██╗       █ █  █ █║
 ╚████╔╝ ███████║██╔██╗ ██║███████║       █      █║ 
  ╚██╔╝  ██╔══██║██║╚██╗██║██╔══██║        ██████ ║
   ██║   ██║  ██║██║ ╚████║██║  ██║       █ ████ █║
   ╚═╝   ╚═╝  ╚═╝╚═╝  ╚═══╝╚═╝  ╚═╝         █  █══╝

EOF

echo -ne $ERR
cat<<EOF 
██╗███╗   ██╗███████╗████████╗ █████╗ ██╗     ██╗     
██║████╗  ██║██╔════╝╚══██╔══╝██╔══██╗██║     ██║     
██║██╔██╗ ██║███████╗   ██║   ███████║██║     ██║     
██║██║╚██╗██║╚════██║   ██║   ██╔══██║██║     ██║     
██║██║ ╚████║███████║   ██║   ██║  ██║███████╗███████╗
╚═╝╚═╝  ╚═══╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚══════╝                                                   
EOF

echo -ne $NORMAL

}

##############
# Menus
##############

# Menu principal
mainMenu(){
	optionsMain=$(whiptail --title "YANA Server $INSTALLVERSION" --menu "" --cancel-button "Annuler" 0 0 0 \
		"Installer" "" \
		"Configurer" "" \
		"Sauvegarder" "" \
		"Restaurer" "" \
		"Quitter" "" 3>&1 1>&2 2>&3)

	case $optionsMain in
		"Installer")
			installMenu;;
		"Configurer")
			setupMenu;;
		"Sauvegarder")
			saveMenu;;
		"Restaurer")
			restoreMenu;;
		*)
			echo -e "$OK ... A la prochaine! $NORMAL"
			;;
	esac
}

# Menu d'installation
installMenu(){
	if(whiptail --title "Installation" --yesno "$installMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		doInstall=1
	else
		echo -e "\033[1;34m... A la prochaine!\033[0;39m"
	fi
}

# Menu de configuration
setupMenu(){
	optionsSetup=$(whiptail --title "YANA Server $INSTALLVERSION" --menu "" --cancel-button "Retour" 0 0 0 \
		"Vérifier YANA" "" \
		"Mettre à jour YANA" "" \
		"Redimensionner la carte SD" "" \
		"Renommer le Raspberry Pi" ""  \
		"Scripts Plugins" "" \
		"Quitter" "" \
		 3>&1 1>&2 2>&3)

	case $optionsSetup in
		"Vérifier YANA")
			checkMenu;;
		"Mettre à jour YANA")
			forceYanaUpdate;;
		"Redimensionner la carte SD")
			resizeSDCard
			setupMenu;;
		"Renommer le Raspberry Pi")
			renameMenu
			setupMenu;;
		"Scripts Plugins")
			scriptsMenu;;
		"Quitter")
			echo -e "$OK ... A la prochaine! $NORMAL";;
		*)
			mainMenu;;
	esac
}

# Menu vérification de yana
checkMenu(){
	if(whiptail --title "Vérification" --yesno "$checkMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		updateRaspberryPi
		checkPermissions
		checkBinariesMenu
		installYanaSocket
		addCron
	else
		setupMenu
	fi
}

resizeSDCardMenu(){
	if(whiptail --title "Carte SD" --yesno "$resizeSDCardMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		resizeSDCard
	fi
}

# Menu de renommage du Raspberry Pi
renameMenu(){
	newhostname=$(whiptail --inputbox "$renameMessage" --title "Choissisez un nom" 0 0 3>&1 1>&2 2>&3)
	renamePi
}

# Menu de scripts pour les plugins
# Il faut créer un script au format .sh pour dans /var/www/yana-server/plugins/nom-du-plugin/nom-du-script.sh
# On peut utiliser les fonctions du script d'installation et les variables à l'intérieur d'un script
# Par example vous pouvez récupérer le nom $HOSTNAME ou l'adresse IP $IPADDRESS
# Vérifier si internet est connecté 
scriptsMenu(){
getAllScripts

while read -r nextScript
do 
	scriptName=$(echo "${nextScript//\/var\/www\/yana-server\/plugins\//}")
	menu_options[ $i ]="$scriptName"
	(( i++ ))
	
	menu_options[ $i ]=""
	(( i++ ))
done <<<"$allScripts"

scriptToExecute=$(whiptail --title "Plugins" --menu "Gérer un Plugin" 0 0 0 "${menu_options[@]}" 3>&1 1>&2 2>&3 )
executeScript
}

executeScript(){
if [[ -f /var/www/yana-server/plugins/$scriptToExecute ]];then
	chmod +x /var/www/yana-server/plugins/$scriptToExecute
	clear
	yanaLogo
	echo -e "$OK -----> Exécution de $scriptToExecute $NORMAL"
	dir=$(dirname /var/www/yana-server/plugins/$scriptToExecute)
	cd $dir;. /var/www/yana-server/plugins/$scriptToExecute
else
	echo -e "$OK -----> Aucun script trouvé dans /var/www/yana-server/plugins/$scriptToExecute $NORMAL"
fi

}

# Menu de vérification des fichiers binaires
checkBinariesMenu(){
	getAllBinaries

	if(whiptail --title "Permissions binaires" --yesno "Je peux automatiquement donner les droits roots aux programmes des plugins\n\nVoici la liste des programmes concernés: \n$allBinaries" --yes-button "Oui" --no-button "Non" 0 0) then
		setupPermissionsBinaries
		whiptail --title "Permissions" --msgbox "Permissions activés" 0 0
	fi

}

saveMenu(){
	if(whiptail --title "Sauvegarde USB" --yesno "$saveMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		saveUSB
	else
		mainMenu
	fi
}

restoreMenu(){
	if(whiptail --title "Restauration USB" --yesno "$restoreMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		restoreUSB
		checkPermissions
		setupPermissionsBinaries
	else
		mainMenu
	fi
}


## Menu d'erreurs


# Menu Internet HS
noInternetMenu(){
	whiptail --title "Vérifier que vous êtes connecté à internet" --msgbox "$noInternetMessage" 0 0
	echo -e "$ERR - Impossible de continuer sans internet $NORMAL"
}

# Menu Apache déjà installé
ApacheAlreadyInstalledMenu(){
	if(whiptail --title "Un serveur web est déjà installé" --yesno "$ApacheMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		doInstallWebServer=1
	else
		doInstallWebServer=0
	fi
}

# Menu Nginx déjà installé
nginxAlreadyInstalledMenu(){
	if(whiptail --title "Un serveur web est déjà installé" --yesno "$nginxMessage" --yes-button "Oui" --no-button "Non" 0 0) then
		doInstallWebServer=1
	else
		doInstallWebServer=0
	fi
}

# Menu error APT-GET
aptgetErrorMenu(){
	#Récupère le message d'erreur apt-get
	getAptError

	#Affiche l'erreur dans la GUI
	whiptail --title "le gestionnaire de paquet ne réponds pas" --msgbox "$aptGetErrorMessage $aptError" 0 0
	echo -e "$ERR Impossible de continuer sans apt-get $NORMAL"
	echo -e "$WARN ERREUR - $aptGetErrorMessage $aptError"
	exit 1
}

# Menu erreur git
gitErrorMenu(){
	#Récupère le message d'erreur de git clone
	getGitError

	#Affiche l'erreur dans la GUI
	whiptail --title "le gestionnaire de paquet ne réponds pas" --msgbox "$gitErrorMessage $gitError" 0 0
	exit 1
}

# Menu erreur wiringPi
wiringPiErrorMenu(){
	getWiringPiError

	whiptail --title "Echec de la compilation de WiringPi" --msgbox "$wiringPiErrorMessage $wiringPiError" 0 0
}

# Menu erreur lighttpd
lighttpdErrorMenu(){
	whiptail --title "Echec du lancement de Lighttpd" --msgbox "$lighttpdErrorMessage $lighttpdError" 0 0
}

confirmEraseUSB(){
	if(whiptail --title "Confirmer la suppression de la sauvegarde" --yesno "Une sauvegarde précédente existe la supprimer ?" --yes-button "Oui" --no-button "Non" 0 0) then
		confirmErase=1
	else
		confirmErase=0
	fi
}



##############
# Scripts
##############
# Toutes les parties de l'installation sont séparés en fonctions
# Ceci afin de faciliter les tests de chaque partie

#Vérifie que vous êtes bien en root
verifyRoot() {
	if [ $(id -u) -ne 0 ]; then
		echo -e "\033[1;31mVous avez oublié de vous mettre en root!\033[0;39m"
		echo -e "Tapez \033[1;34msudo $0\033[0;39m"
		isRoot=0
	else
		isRoot=1
	fi
}

#Vérifie l'état de la connexion internet
checkInternet(){
	ping -c1 www.github.com > /dev/null 2>&1 && internet=1 || internet=0
	echo -e "$OK -----> Vérification de la connexion à internet $NORMAL"
	if [[ $internet -eq 0 ]]
		then
			noInternetMenu
	fi
}

#Récupère le message d'erreur APT-GET
getAptError(){
	rm -f /tmp/aptError.log

	#On lance apt-get update en dry-run et on sauve le log dans /tmp/aptError.log
	apt-get update -s -q -y > /tmp/aptError.log 2>&1
	aptError=$(cat /tmp/aptError.log)
}

#Récupère le message d'erreur de git
getGitError(){
	gitError=$(cat /tmp/gitError.log)
}

#Récupère le message d'erreur de WiringPi
getWiringPiError(){
	wiringPiError=$(cat /tmp/wiringPiError.log)
}

#Récupère le message d'erreur de lighttpd
getLighttpdError(){
	lighttpdError=$(cat /tmp/lighttpdReload.log)
}

#Met à jour le Raspberry Pi en utilisant whiptail comme interface
updateRaspberryPi(){
	echo -e "$OK -----> Mise à jour du Raspberry Pi $NORMAL"

	#debconf-apt-progress permet d'afficher la progression de la mis à jour dans une GUI en français
	debconf-apt-progress -- apt-get -q -y update
	globalError=$?
	if [[ $globalError -ne 0 ]];then
		aptgetErrorMenu
	fi
	debconf-apt-progress -- apt-get -q -y upgrade
	globalError=$?
	if [[ $globalError -ne 0 ]];then
		aptgetErrorMenu
	fi

	#On installe aussi le client git
	debconf-apt-progress -- apt-get install -q -y git-core
	if [[ $globalError -ne 0 ]];then
		aptgetErrorMenu
	fi
}

#Change les locales de l'anglais au français de manière non interactive
setLocaleToFrench(){
	echo -e "$OK -----> Configuration du terminal en français... Patientez s'il vous plaît ... $NORMAL"

	#Ajout des locales FR
	sed -i -e 's/# fr_FR.UTF-8 UTF-8/fr_FR.UTF-8 UTF-8/' /etc/locale.gen

	#Met FR en locale par défaut
	echo 'LANG="fr_FR.UTF-8 UTF-8"'>/etc/default/locale
	update-locale LANG=fr_FR.UTF-8
	export LANG=fr_FR.UTF-8

	#Les locales sont installés silencieusement
	dpkg-reconfigure --frontend=noninteractive locales > /tmp/localeSetup.log 2>&1
	globalError=$?
	#En cas d'erreur on affiche le message
	if [[ $globalError -ne 0 ]];then
		localeError=$(cat /tmp/localeSetup.log)
		whiptail --title "Locales FR" --msgbox "$localeMessage $localeError" 0 0
	fi
}

#Gestion automatique des fuseaux horaires à l'aide de tzupdate
configureTimeZone(){
	echo -ne "$OK -----> Configuration du fuseau horaire $NORMAL"

	#Vérifie que Python PIP est disponible
	debconf-apt-progress -- apt-get install -q -y python-pip
	globalError=$?
	if [[ $globalError -ne 0 ]];then
		aptgetErrorMenu
	fi

	#Installation silencieuse du Package python tzupdate
	pip install --quiet tzupdate

	#Si l'installation c'est correctement passé lancé tzupdate silencieusement
	if [ -f /usr/local/bin/tzupdate ];then
		tzupdate > /dev/null 2>&1

		#On récupère après la zone géographique pour l'afficher
		currentTimeZone=$(tzupdate -p|awk '{print $4}')
		echo -e "$WARN : $currentTimeZone $NORMAL"
	else
		echo -e "$ERR Impossible de changer le fuseau horaire automatiquement (ce n'est pas nécessaire) $NORMAL"
	fi
}

#Vérification sommaire de l'existance d'autres serveur web
#Si un autre serveur web est installé prévient l'utilisateur
#Afin qu'il choissisent s'il veut installer lighttpd ou pas
checkWebServer(){
	if [ -f "/usr/sbin/apache2" ];then
		ApacheAlreadyInstalledMenu
	fi

	if [ -f "/usr/sbin/nginx" ];then
		nginxAlreadyInstalledMenu
	fi
}

#Installation du serveur web et de SQLite
installWebServer(){
	echo -e "$OK -----> Installation du serveur web $NORMAL"
	debconf-apt-progress -- apt-get install -q -y lighttpd git-core sqlite3 php5-sqlite php5-common php5-cgi php5-cli
	if [[ $globalError -ne 0 ]];then
		aptgetErrorMenu
	fi

	#On efface la page par défaut de lighttpd pour éviter d'embrouiller les utilisateurs
	rm -f /var/www/index.lighttpd.html
	rm -rf /var/www/html

}

#Configure lighttpd pour bloquer l'accès à la base de données
setupWebServer(){
echo -e "$OK -----> Configuration du serveur web (/etc/lighttpd/lighttpd.conf) $NORMAL"

cat <<\EOF > /etc/lighttpd/lighttpd.conf
server.modules = (
        "mod_access",
        "mod_alias",
        "mod_compress",
        "mod_redirect",
#       "mod_rewrite",
)

server.document-root        = "/var/www/yana-server/"
server.upload-dirs          = ( "/var/cache/lighttpd/uploads" )
server.errorlog             = "/var/log/lighttpd/error.log"
server.pid-file             = "/var/run/lighttpd.pid"
server.username             = "www-data"
server.groupname            = "www-data"
server.port                 = 80

index-file.names            = ( "index.php", "index.html", "index.lighttpd.html" )
url.access-deny             = ( "~", ".inc", "db","log.txt" )
static-file.exclude-extensions = ( ".php", ".pl", ".fcgi" )

compress.cache-dir          = "/var/cache/lighttpd/compress/"
compress.filetype           = ( "application/javascript", "text/css", "text/html )                                                                                                                                                             ", "text/plain" )

# default listening port for IPv6 falls back to the IPv4 port
include_shell "/usr/share/lighttpd/use-ipv6.pl " + server.port
include_shell "/usr/share/lighttpd/create-mime.assign.pl"
include_shell "/usr/share/lighttpd/include-conf-enabled.pl"
EOF

lighttpdSetupError=$?

if [[ lighttpdSetupError -eq 1 ]];then
	echo -e "$ERR - Le fichier /etc/lighttpd/lighttpd.conf n'a pas été modifié $NORMAL"
fi

#Activation de PHP et rechargement de lighttpd
	lighty-enable-mod fastcgi-php > /dev/null 2>&1
	service lighttpd restart > /tmp/lighttpdReload.log 2>&1
	globalError=$?
	if [[ $globalError -ne 0 ]];then
		getLighttpdError
		lighttpdErrorMenu
		echo -e "$ERR - La configuration de /etc/lighttpd/lighttpd.conf a échoué $NORMAL"
		echo -e "$WARN ERREUR: $lighttpdError $NORMAL"
	fi

}




#Clonage de Yana
#Si Yana a déjà été cloné alors on propose à l'utilisateur de le réinstaller
cloneYana(){
	if [[ copyYana -eq 1 ]];then
		if [[ -d "/var/www/yana-server" ]];then
				if(whiptail --title "Yana déjà installé" --yesno "$yanaMessage" --yes-button "Oui" --no-button "Non" 0 0) then
					echo -e "$ERR -----> Réinstallation de Yana Server $NORMAL"
					rm -rf /var/www/yana-server
					git clone https://github.com/ldleman/yana-server.git /var/www/yana-server > /tmp/gitError.log 2>&1
					globalError=$?
					if [[ $globalError -ne 0 ]];then
						gitErrorMenu
					fi
				fi
		else
			echo -e "$OK -----> Copie de Yana Server $NORMAL"
			git clone https://github.com/ldleman/yana-server.git /var/www/yana-server > /tmp/gitError.log 2>&1
			globalError=$?
			if [[ $globalError -ne 0 ]];then
				gitErrorMenu
			fi
		fi
	fi
}

# Mis à jour forcé de Yana
# Cela n'affectera pas la base de données 
forceYanaUpdate(){
yanaLogo
echo -e "$OK -----> Mise à jour de Yana Server $NORMAL"

cd /var/www/yana-server && git fetch --all > /dev/null 2>&1
fetchStatus=$?
cd /var/www/yana-server && lastcomment=$(git reset --hard origin/master | awk '{$1="";$2="";$3="";$4="";$5="";print $0;}') > /dev/null 2>&1
resetStatus=$?
cd /var/www/yana-server && pullLog=$(git pull origin master) > /dev/null 2>&1
pullStatus=$?

if [[ $fetchStatus -eq 0 ]] && [[ $resetStatus -eq 0 ]] && [[ $pullStatus -eq 0 ]];then
	
	echo -e "$INFO -----> Dernier statut - $lastcomment $NORMAL"
	# On revérifie les permissions
	checkPermissions
	setupPermissionsBinaries
else
	echo -e "$ERR -----> La mise à jour a échoué $NORMAL"
	echo $pullLog
fi



}

#Vérification des permissions pour Yana Server et le plugin radioRelay
checkPermissions(){
	echo -e "$OK -----> Vérification des permissions de YANA $NORMAL"
	chown -R www-data:www-data /var/www/yana-server
	chmod 750 -R /var/www/yana-server

	giveRootPermissions /var/www/yana-server/plugins/radioRelay/radioEmission
	
}

# Cherche tout les fichiers cpp dans yana-server
getAllBinaries(){
	allBinaries=$(find /var/www/yana-server/plugins -name "*.cpp")
}

# Cherche tout les fichiers cpp pour donner la permission root au fichier binaire associés
setupPermissionsBinaries(){
	while read -r file; do
		file=$(echo "${file/.cpp/}")
		if [[ -f $file ]];then
	    	giveRootPermissions $file
	    fi
	done <<< "$allBinaries"
}

# Cherche des scripts dans les plugins
getAllScripts(){
	allScripts=$(find /var/www/yana-server/plugins -name "*.sh")
	if [[ -z "${allScripts// }" ]];then
		allScripts="Aucun Script disponible"
	fi
}

# Donne les permissions root au serveur web à un programme
# Les permissions ont été géré de façon à limité au maximum l'accès
giveRootPermissions(){
	rootProgram=$1
	chown root:www-data $rootProgram
	chmod 000 $rootProgram
	chmod +sx $rootProgram
}

# Installation de wiringPi dans /opt/wiringPi
# Une fois installé , wiringPi n'utilisera pas ce dossier qui ne contient que les sources
installWiringPi(){
	#Vérifie si WiringPi est installé sinon on ne l'installe pas
	if [[ ! -f /usr/local/bin/gpio ]];then
		echo -e "$OK -----> Copie de wiringPi $NORMAL"

		#Si les sources ont déjà été copié on les efface pour les retélécharger
		if [ -d /opt/wiringPi ];then
			rm -rf /opt/wiringPi
		fi

		cd /opt/
		git clone git://git.drogon.net/wiringPi /opt/wiringPi > /tmp/gitError.log 2>&1
		globalError=$?
		if [[ $globalError -ne 0 ]];then
			gitErrorMenu
		fi
		cd /opt/wiringPi/
		echo -e "$OK -----> Installation de wiringPi $NORMAL"
		./build > /tmp/wiringPiError.log 2>&1
		globalError=$?
		if [[ $globalError -ne 0 ]];then
			wiringPiErrorMenu
		fi
	fi
}

#Création d'un lien symbolique de l'installateur vers 
linkInstaller(){
	if [[ -f /usr/local/bin/configurer ]];then
		rm /usr/local/bin/configurer
	fi
	ln -s /var/www/yana-server/install.sh /usr/local/bin/configurer
	chmod +x /usr/local/bin/configurer
}

# Installation du socket pour le client YANA
installYanaSocket(){

echo -e "$OK -----> Installation du socket YANA $NORMAL"

if [[ -s /var/www/yana-server/db/.database.db ]];then
	# Installation de supervisor
	debconf-apt-progress -- apt-get install -q -y supervisor
	globalError=$?
	if [[ $globalError -ne 0 ]];then
		aptgetErrorMenu
	fi

	# Configuration du socket dans supervisor
cat <<\EOF > /etc/supervisor/conf.d/yana.conf
[program:yana]
command=/usr/bin/php /var/www/yana-server/socket.php
autostart=true
autorestart=true
stdout_logfile=/var/log/yanaSocket.log
redirect_stderr=true
EOF

	supervisorFatalError=0

	# Lecture du fichier de configuration
	supervisorctl reread > /tmp/supervisorReReadError.log 2>&1
	supervisorError=$(cat /tmp/supervisorReReadError.log)
	if [[ ! $supervisorError == "No config updates to processes" && ! $supervisorError == "yana: available" ]];then
		echo -e "$ERR Erreur dans /etc/supervisor/conf.d/yana.conf - $supervisorError $NORMAL"
		supervisorFatalError=1
	fi

	# Ajout du socket dans supervisor
	supervisorctl update > /tmp/supervisorUpdateError.log 2>&1
	supervisorErrorUpdate=$(cat /tmp/supervisorUpdateError.log)
	if [[ $supervisorError == "" && ! $supervisorError == "yana: added process group" ]];then
		echo -e "$ERR Erreur dans /etc/supervisor/conf.d/yana.conf - $supervisorError $NORMAL"
		supervisorFatalError=1
	fi

	# Relancement du socket pour test
	if [[ $supervisorFatalError -eq 0 ]];then
		supervisorctl stop yana > /tmp/supervisorStopError.log 2>&1
		supervisorctl start yana > /tmp/supervisorStartError.log 2>&1

		supervisorStartError=$(cat /tmp/supervisorStartError.log)
		if [[ ! $supervisorStartError == "yana: started" ]];then
			echo -e "$ERR Erreur lancement du socket - $supervisorStartError $NORMAL"
			echo -e "$ERR Tapez $INFO sudo cat /var/log/yanaSocket.log pour plus d'information $NORMAL"
		fi
	fi

else
	echo -e "$ERR Aller sur $INFO http:///$HOSTNAME.local $ERR pour finalisez l'installation avant d'installer le socket $NORMAL"
fi

}
	
checkCron(){
	checkCronYana=$(crontab -l|grep 'http://localhost/action.php?action=crontab')
}

addCron(){
	echo -e "$OK -----> Installation du cron scénario $NORMAL"

	if [[ -z $checkCronYana ]];then
		crontab -l | { cat; echo '*/1 * * * * wget "http://localhost/action.php?action=crontab" -O /dev/null 2>&1'; } | crontab -
	else
			echo -e "$WARN -----> Installation du cron scénario (déjà effectué) $NORMAL"	
	fi
}

#Afin de sécuriser Yana une fois l'installation fini, on supprime install.php
#Et on change le mot de passe de l'utilisateur par défaut (pi)
securityCheck(){
	if [[ -f /var/www/yana-server/install.php ]];then
		echo -e "$OK -----> Supression de install.php $NORMAL"
		rm /var/www/yana-server/install.php
		
	fi
}

# Pour redimensionner la carte sd, j'utilise raspi-config en mode unattended
resizeSDCard(){
	echo -e "$OK -----> Vérification du redimensionnement de la carte SD $NORMAL"
	raspi-config --expand-rootfs > /tmp/resizeSDCardError.log 2>&1
}

endInstall(){
	HOSTNAME=$(cat /etc/hostname)
	echo -e "$OK -----> Finissez l'installation en allant sur votre $WARNING navigateur $OK à $INFO http://$HOSTNAME.local $NORMAL"
	echo -ne "$WARN ATTENTE DE L'UTILISATEUR $NORMAL"
	databaseCreated=0
	while [[ databaseCreated -eq 0 ]]
	do
		if [ ! -s /var/www/yana-server/db/.database.db ];then
			echo -ne "."
		else
			echo -e "$OK --> OK! $NORMAL"
			databaseCreated=1
		fi
		sleep 3
	done
}

#http://unix.stackexchange.com/questions/60299/how-to-determine-which-sd-is-usb par F.Hauri
getUSB(){
	cut -d/ -f4 <(grep -vl ^0$ $(sed s@device/.*@size@ <(grep -l ^DRIVER=sd $(sed s+/rem.*$+/dev*/ue*+ <(grep -Hv ^0$ /sys/block/*/removable)) <(:))) <(:))

}

saveUSB(){
	yanaLogo
	USBDrive="/dev/$(getUSB)1"

	#Si aucun lecteur n'est detecté
	if [[ $USBDrive == "/dev/1" ]];then
		echo -e "$WARN -----> Aucun clé USB detecté ! $NORMAL"
	else
		echo -e "$OK -----> Clé USB trouvé sur $INFO $USBDrive $NORMAL"
		
		#Si le lecteur existe vraiment
		if [[ -e "$USBDrive" ]];then
		
			# Si la clé est déjà monté, on l'a démonte par sécurité
			if mount |grep "/media/backupUSB" > /dev/null;then
				umount /media/backupUSB
				#@todo rajouter un test
			fi

			# Si le dossier de montage n'existe pas on le crée
			if [ ! -d /media/backupUSB ];then
				mkdir /media/backupUSB
			fi
		
			# On monte la clé USB
			mount "$USBDrive" /media/backupUSB
			mountState=$?

			# Si le montage s'est déroulé correctement
			if [[ $mountState -eq 0 ]];then

				#Si une sauvegarde précédente, on prévient l'utilisateur
				if [ -d /media/backupUSB/yana ];then
					confirmEraseUSB
					#On efface la sauvegarde précédente
					if [[ confirmErase -eq 1 ]];then
						rm -rf /media/backupUSB/yana
						echo -e "$WARN -----> Supression de la sauvegarde précédente $NORMAL"
					else
						echo -e "$WARN -----> Annulation de la sauvegarde"
					fi
				else 
					confirmErase=1
				fi

				#Si pas de sauvegarde précédente ou confirmation de la suppression
				if [[ confirmErase -eq 1 ]];then
					echo -e "$OK -----> Copie des fichiers $NORMAL"
					cp -R /var/www/yana-server/ /media/backupUSB/yana
					copyState=$?

					if [[ $copyState -eq 0 ]];then
						echo -e "$OK -----> Copie réussi avec succès ! $NORMAL"

					else
						echo -e "$ERR -----> La copie a échoué ! $NORMAL"
					fi
				fi

				# Quoiqu'il arrive nous démontons la clé à la fin				
				echo -e "$OK -----> Démontage de la clé USB $NORMAL"
				umount /media/backupUSB
				umountState=$?
				
				#Si l'état de la clé n'est pas correcte
				if [ $umountState -eq 0 ];then
					echo -e "$OK -----> Vous pouvez retirer votre clé en toute sécurité $NORMAL"
				else
					echo -e "$ERR -----> La clé n'a pas été correctement démonté $NORMAL "
					echo -e "$INFO Vous pouvez la démonter manuellement en tapant $OK umount /media/backupUSB $NORMAL"
				fi
			
			else
				echo -e "$ERR -----> Impossible de monter ! $NORMAL"	
			fi

		else
			echo -e "$ERR -----> La détection a échoué ! $NORMAL"
		fi	
	fi
}

restoreUSB(){
	yanaLogo
	USBDrive="/dev/$(getUSB)1"

	#Si aucun lecteur n'est detecté
	if [[ $USBDrive == "/dev/1" ]];then
		echo -e "$WARN -----> Aucun clé USB detecté ! $NORMAL"
	else
		echo -e "$OK -----> Clé USB trouvé sur $INFO $USBDrive $NORMAL"
		
		#Si le lecteur existe vraiment
		if [[ -e "$USBDrive" ]];then
		
			# Si la clé est déjà monté, on l'a démonte par sécurité
			if mount |grep "/media/backupUSB" > /dev/null;then
				umount /media/backupUSB
				#@todo rajouter un test
			fi

			# Si le dossier de montage n'existe pas on le crée
			if [ ! -d /media/backupUSB ];then
				mkdir /media/backupUSB
			fi
		
			# On monte la clé USB
			mount "$USBDrive" /media/backupUSB
			mountState=$?

			# Si le montage s'est déroulé correctement
			if [[ $mountState -eq 0 ]];then

				rm -rf /var/www/yana-server
				echo -e "$WARN -----> Supression de yana-server $NORMAL"

				echo -e "$OK -----> Restauration de la sauvegarde $NORMAL"
				cp -R /media/backupUSB/yana /var/www/yana-server/ 
				copyState=$?

				if [[ $copyState -eq 0 ]];then
					echo -e "$OK -----> La restauration est un succès ! $NORMAL"

				else
					echo -e "$ERR -----> La restauration a échoué ! $NORMAL"
				fi
				

				# Quoiqu'il arrive nous démontons la clé à la fin				
				echo -e "$OK -----> Démontage de la clé USB $NORMAL"
				umount /media/backupUSB
				umountState=$?
				
				#Si l'état de la clé n'est pas correcte
				if [ $umountState -eq 0 ];then
					echo -e "$OK -----> Vous pouvez retirer votre clé en toute sécurité $NORMAL"
					echo -e "$INFO Pour réouvrir la clé USB tapez : $ERR mount $USBDrive /media/backupUSB $NORMAL"
				else
					echo -e "$ERR -----> La clé n'a pas été correctement démonté $NORMAL "
					echo -e "$INFO Vous pouvez la démonter manuellement en tapant $OK umount /media/backupUSB $NORMAL"
				fi
			
			else
				echo -e "$ERR -----> Impossible de monter ! $NORMAL"	
			fi

		else
			echo -e "$ERR -----> La détection a échoué ! $NORMAL"
		fi	
	fi
}

# Renomme le Raspberry Pi sur le réseau
# Il sera alors accessible depuis newhostname.local
# Cela va modifier /etc/hosts et /etc/hostname où le daemon avahi
# va chercher le nom du Raspberry Pi
renamePi(){

yanaLogo
if [[ -z $newhostname ]];then
	newhostname="maison"
fi
		
cat <<EOF > /etc/hosts && 
127.0.0.1       localhost
::1             localhost ip6-localhost ip6-loopback
fe00::0         ip6-localnet
ff00::0         ip6-mcastprefix
ff02::1         ip6-allnodes
ff02::2         ip6-allrouters

127.0.1.1       $newhostname
EOF

echo $newhostname > /etc/hostname

hostname $newhostname
service avahi-daemon restart > /dev/null 2>&1
whiptail --title "Raspberry Pi s'appelle $newhostname.local" --msgbox "Tapez http://$newhostname.local/ pour accéder à Yana" 0 0
echo -ne "$OK -----> Renommage du Raspberry Pi $NORMAL"
echo -e "$WARN : $newhostname.local $NORMAL"
}

###############
# La partie principale
###############
# Si vous voulez supprimer des étapes ou en rajouter
# C'est ici que tout se passe

#Vérification des droits administrateur
cd /
verifyRoot

if [[ $isRoot -eq 1 ]];then

	
	if [[ $# -eq 1 ]];then
		scriptToExecute=$1
		
		if [[ scriptToExecute -eq "noresize" ]];then
			resizeSDCard=0
		fi

	else


		# Affichage du menu principal
		mainMenu

		# Si Installer est appuyé
		if [[ $doInstall -eq 1 ]];then


			if [ $resizeSD -eq 1 ];then	
				resizeSDCardMenu # Redimensionnement de la carte SD
			fi

			# Renommer le Raspberry Pi
			renameMenu

			# Vérifier si github.com est accessible
			checkInternet
			if [[ $internet -eq 1 ]];then

				setLocaleToFrench # Mettre le terminal en français
				updateRaspberryPi # Mettre à jour le Raspberry Pi (apt-get update/upgrade)
				configureTimeZone # Configurer le fuseau horaire automatiquement (tzupdate)

				# Clone le repo ldleman/yana
				cloneYana

				# Installe le serveur web
				checkWebServer
				if [[ $doInstallWebServer -eq 1 ]];then
				 	installWebServer
				 	setupWebServer
				fi
		
				# Vérifie les permissions des fichiers et des binaires
				checkPermissions
			
				# Install WiringPi (gpio)
				installWiringPi

				# Fait un lien de /var/www/yana-server/install.sh ver /usr/local/bin/configuration
				linkInstaller
			
				# Affiche un message avec l'étape sur le web
				endInstall
				securityCheck
				checkBinariesMenu
				installYanaSocket
				addCron
				echo -e "$OK Installation TERMINE!!! Il est conseillé de $ERR redémarrer $OK votre raspberry pi $NORMAL"
				echo -e "$INFO sudo reboot $NORMAL"
			fi
		fi
	fi
fi
