<?php
	/* Nom du programme */ 
	define('PROGRAM_NAME','Yana Server');
	/* Nom de l'auteur principal */
	define('PROGRAM_AUTHOR','Valentin CARRUESCO');
	/* Préfixe de la base de données */ 
	define('MYSQL_PREFIX','yana_');
	/* Remplace MYSQL_PREFIX qui est deprecated */
	define('ENTITY_PREFIX', MYSQL_PREFIX);
	/* Chemin vers la base SQLITE */
	define('DB_NAME','db/.database.db');
	/* Chaine de connexion sql */
	define('BASE_CONNECTION_STRING','sqlite:'.DB_NAME);
	/* Chemin vers le fichier de logs */
	define('LOG_FILE','log/.log.txt');
	/* Chemin vers le cache des avatars */
	define('AVATAR_FOLDER','cache/avatar');
	/* Chemin http vers yana */
	define('YANA_URL','http://127.0.0.1:80/yana-server');
	/* Port du serveur socket */
	define('SOCKET_PORT',9999);
	/* Nombre maxium de clients sur le serveur socket */
	define('SOCKET_MAX_CLIENTS',20);
	/* Chemin absolus vers le projet */
	define('__ROOT__',realpath(dirname(__FILE__)));
	/* Alias de fainéant */
	define('SLASH',DIRECTORY_SEPARATOR);

	/* 
	* <!> Laisser à vide sauf si vous souhaitez vous auto-logguer avec un compte sans mot de passe 
	*     Ceci peut être utile pour les yana-server accessible uniquement depuis votre réseau interne
	*     Dans tous les autres cas, il serait insécurisé et donc déconseillé d'utiliser cette option.
	*     Pour vous auto-loguer avec un compte de la base yana, ecrivez le login de ce compte dans cette constante
	*/
	define('AUTO_LOGIN','');
?>