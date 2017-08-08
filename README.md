Yana Server
===========

Interface PHP de domotique Y.A.N.A (You Are Not Alone) v4.0

Pré-requis
============

- Raspberry PI
- Apache 2 ou Lighttpd
- PHP 5
- MySQL ou MariaDb ou SQLite 3
- [Librairie Wiring PI](https://projects.drogon.net/raspberry-pi/wiringpi/download-and-install/)


Installation 
============


Sécurité
========
Pour des raisons de sécurité, il est très fortement déconseillé d'ouvrir l'accès au serveur web de yana sur l'exterieur.
Si vous le faites cependant et que vous utilisez une base sqlite, il est necessaire d'utiliser apache comme serveur http OU de configurer votre serveur http
pour interdire l'accès au dossier /db

Mise à jour
============

Pour mettre a jour yana-server, il faut utiliser git, placez vous dans le répertoire de yana
```cd /var/www/yana-server```

Et faites un git pull pour récuperer la dernière version
```git pull```

Puis remettez les permission en ecriture sur le dossier plugins
> sudo chown -R www-data:www-data /var/www/yana-server && sudo chown root:www-data /var/www/yana-server/plugins/radioRelay/radioEmission && sudo chmod +s /var/www/yana-server/plugins/radioRelay/radioEmission

Une fois l'update terminé, allez en section plugin de yana-server et désactivez/réactivez chaques plugins utilisés afin de mettre à jour leurs tables.
