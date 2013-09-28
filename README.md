Yana Server
===========

Interface PHP de domotique Y.A.N.A (You Are Not Alone)

Pré-requis
============

- Raspberry PI
- Apache 2 ou Lighttpd
- PHP 5
- SQLite 3

Installation
============

Executez les commandes suivantes dans un shell :

`sudo apt-get install git-core && sudo apt-get install sqlite3 && sudo apt-get install php5-sqlite && cd /var/www/ && sudo git clone https://github.com/ldleman/yana-server.git /var/www/yana-server && sudo chown -R www-data:www-data yana-server && sudo chown root:www-data /var/www/yana-server/plugins/relay/radioEmission && sudo chmod +s /var/www/yana-server/plugins/relay/radioEmission`

Puis executez l'adresse web de yana dans un navigateur :

`http://adresse.de.votre.rpi/yana-server`

Et suivez le formulaire d'installation.

A la fin de l'installation, vous pouvez activer ou désactiver les plugins qui vous sont utiles dans la section
configuration --> plugins

Mise à jour
============

Pour mettre a jour yana-server, il faut utiliser git, placez vous dans le répertoire de yana
```cd /var/www/yana-server```

Et faites un git pull pour récuperer la dernière version
```git pull```

Attention, si vous aviez fait des modifs sur le code entre temps il est possible que le git pull ne fonctionne pas, dans ce cas faites un git checkout pour reprendre la copie exacte du dépot officiel en ecransant vos modifications
```git checkout origin```

Une fois l'update terminé, allez en section plugin de yana-server et désactivez/réactivez chaques plugins utilisés afin de mettre à jour leurs tables.